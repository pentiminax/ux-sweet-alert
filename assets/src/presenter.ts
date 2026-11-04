import Swal, {type SweetAlertResult} from 'sweetalert2';
import {getLiveItemParams} from './dom-utils.js';
import {
    AFTER_FIRE_EVENT,
    ALERT_CLOSED_EVENT_PREFIX,
    BEFORE_FIRE_EVENT,
    CALLBACK_RESPONSE_EVENT,
    type AlertPayload,
} from './types.js';

let queue: Promise<unknown> = Promise.resolve();
let guardInstalled = false;

export function installNavigationGuard(): void {
    if (guardInstalled) {
        return;
    }
    guardInstalled = true;

    document.addEventListener('turbo:before-render', () => {
        if (Swal.isVisible()) {
            Swal.close();
        }
    });
}

export function present(payload: AlertPayload, origin: HTMLElement | Document, callback?: string): Promise<SweetAlertResult> {
    const run = queue.then(() => show(payload, origin, callback));
    queue = run.catch(() => undefined);
    return run;
}

async function show(payload: AlertPayload, origin: HTMLElement | Document, callback?: string): Promise<SweetAlertResult> {
    const {id, callbackUrl, ...swalOptions} = payload;

    const beforeFire = new CustomEvent(BEFORE_FIRE_EVENT, {
        bubbles: true,
        cancelable: true,
        detail: {swalOptions},
    });
    origin.dispatchEvent(beforeFire);

    let result: SweetAlertResult;

    if (beforeFire.defaultPrevented) {
        result = {isConfirmed: false, isDenied: false, isDismissed: true, dismiss: Swal.DismissReason.cancel};
    } else {
        result = await Swal.fire(swalOptions);

        origin.dispatchEvent(new CustomEvent(AFTER_FIRE_EVENT, {
            bubbles: true,
            cancelable: false,
            detail: {result, swalOptions},
        }));

        await runCallback(result, origin, callbackUrl, callback);
    }

    if (id) {
        document.dispatchEvent(new CustomEvent(`${ALERT_CLOSED_EVENT_PREFIX}${id}:closed`, {detail: result}));
    }

    return result;
}

async function runCallback(result: SweetAlertResult, origin: HTMLElement | Document, callbackUrl?: string, callback?: string): Promise<void> {
    if (callbackUrl) {
        await postCallbackResult(callbackUrl, result, origin);
        return;
    }

    if (typeof callback === 'string' && typeof window[callback] === 'function') {
        (window[callback] as (result: SweetAlertResult) => void)(result);
        return;
    }

    const liveElement = origin instanceof Element ? origin.closest<HTMLElement>('[data-controller~="live"]') : null;
    if (!liveElement) {
        return;
    }

    try {
        const {getComponent} = await import('@symfony/ux-live-component');
        const component = await getComponent(liveElement);
        await component.action('callbackAction', {
            result,
            args: getLiveItemParams(component.element),
        });
    } catch (err) {
        console.warn(err);
    }
}

async function postCallbackResult(callbackUrl: string, result: SweetAlertResult, origin: HTMLElement | Document): Promise<void> {
    try {
        const response = await fetch(callbackUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                isConfirmed: result.isConfirmed,
                isDenied: result.isDenied,
                isDismissed: result.isDismissed,
                value: result.value ?? null,
            }),
            redirect: 'follow',
        });

        if (response.redirected) {
            window.location.href = response.url;
            return;
        }

        const detail = response.headers.get('Content-Type')?.includes('application/json')
            ? await response.json()
            : null;

        origin.dispatchEvent(new CustomEvent(CALLBACK_RESPONSE_EVENT, {
            bubbles: true,
            detail,
        }));
    } catch (err) {
        console.warn('[ux-sweet-alert] Callback URL request failed:', err);
    }
}
