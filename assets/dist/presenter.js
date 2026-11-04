import Swal from 'sweetalert2';
import { getLiveItemParams } from './dom-utils.js';
import { AFTER_FIRE_EVENT, ALERT_CLOSED_EVENT_PREFIX, BEFORE_FIRE_EVENT, CALLBACK_RESPONSE_EVENT, } from './types.js';
let queue = Promise.resolve();
let guardInstalled = false;
export function installNavigationGuard() {
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
export function present(payload, origin, callback) {
    const run = queue.then(() => show(payload, origin, callback));
    queue = run.catch(() => undefined);
    return run;
}
async function show(payload, origin, callback) {
    const { id, callbackUrl, ...swalOptions } = payload;
    const beforeFire = new CustomEvent(BEFORE_FIRE_EVENT, {
        bubbles: true,
        cancelable: true,
        detail: { swalOptions },
    });
    connected(origin).dispatchEvent(beforeFire);
    let result;
    if (beforeFire.defaultPrevented) {
        result = { isConfirmed: false, isDenied: false, isDismissed: true, dismiss: Swal.DismissReason.cancel };
    }
    else {
        result = await Swal.fire(swalOptions);
        connected(origin).dispatchEvent(new CustomEvent(AFTER_FIRE_EVENT, {
            bubbles: true,
            cancelable: false,
            detail: { result, swalOptions },
        }));
        try {
            await runCallback(result, connected(origin), callbackUrl, callback);
        }
        catch (err) {
            console.error('[ux-sweet-alert] Alert callback failed:', err);
        }
    }
    if (id) {
        document.dispatchEvent(new CustomEvent(`${ALERT_CLOSED_EVENT_PREFIX}${id}:closed`, { detail: result }));
    }
    return result;
}
function connected(origin) {
    return origin.isConnected ? origin : document;
}
async function runCallback(result, origin, callbackUrl, callback) {
    if (callbackUrl) {
        await postCallbackResult(callbackUrl, result, origin);
        return;
    }
    if (typeof callback === 'string' && typeof window[callback] === 'function') {
        window[callback](result);
        return;
    }
    const liveElement = origin instanceof Element ? origin.closest('[data-controller~="live"]') : null;
    if (!liveElement) {
        return;
    }
    try {
        const { getComponent } = await import('@symfony/ux-live-component');
        const component = await getComponent(liveElement);
        await component.action('callbackAction', {
            result,
            args: getLiveItemParams(component.element),
        });
    }
    catch (err) {
        console.warn(err);
    }
}
async function postCallbackResult(callbackUrl, result, origin) {
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
    }
    catch (err) {
        console.warn('[ux-sweet-alert] Callback URL request failed:', err);
    }
}
//# sourceMappingURL=presenter.js.map