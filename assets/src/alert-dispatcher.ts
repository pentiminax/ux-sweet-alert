import Swal, {SweetAlertOptions, SweetAlertResult} from 'sweetalert2';
import {BEFORE_FIRE_EVENT, AFTER_FIRE_EVENT, type PreConfirmOptions, type WizardOptions} from './types.js';

// Resolves to true once a wizard is stopped. shortcut: entries are never removed (one per wizard per page), add cleanup if pages run many wizards.
const wizards = new Map<string, Promise<boolean>>();

/**
 * Steps sharing a wizardId fire one after another, whatever transport delivered them (Turbo streams render concurrently).
 * A dismissed step (cancel, Esc, backdrop, close button, timer) stops the wizard: the remaining steps resolve to null
 * without opening, so callers skip their callback and closed event. A deny answer is a valid answer and does not stop it.
 */
export function fireAlert(options: SweetAlertOptions & WizardOptions, targetElement: HTMLElement | Document): Promise<SweetAlertResult | null> {
    const {wizardId, ...swalOptions} = options;
    if (!wizardId) {
        return fireSwal(swalOptions, targetElement);
    }

    const step = (wizards.get(wizardId) ?? Promise.resolve(false))
        .then((stopped) => stopped ? null : fireSwal(swalOptions, targetElement));
    wizards.set(wizardId, step.then((result) => result?.isDismissed ?? true, () => true));

    return step;
}

async function fireSwal(swalOptions: SweetAlertOptions, targetElement: HTMLElement | Document): Promise<SweetAlertResult> {
    const beforeFireEvent = new CustomEvent(BEFORE_FIRE_EVENT, {
        bubbles: true,
        cancelable: true,
        detail: {swalOptions},
    });

    targetElement.dispatchEvent(beforeFireEvent);

    if (beforeFireEvent.defaultPrevented) {
        return {isConfirmed: false, isDenied: false, isDismissed: true, dismiss: Swal.DismissReason.cancel};
    }

    const result = await Swal.fire(withPreConfirm(swalOptions));

    targetElement.dispatchEvent(new CustomEvent(AFTER_FIRE_EVENT, {
        bubbles: true,
        cancelable: false,
        detail: {result, swalOptions},
    }));

    return result;
}

// preConfirmUrl: POST the value while the modal is open; a non-2xx answer keeps it open and shows its `message`.
function withPreConfirm(swalOptions: SweetAlertOptions & PreConfirmOptions): SweetAlertOptions {
    const {preConfirmUrl, preConfirmErrorMessage, ...options} = swalOptions;
    if (!preConfirmUrl) {
        return options;
    }

    const fallback = preConfirmErrorMessage ?? 'Request failed';
    const guard = (orig: boolean | (() => boolean) | undefined) => orig === false ? false : () =>
        !Swal.isLoading() && (typeof orig === 'function' ? orig() : true);

    return {
        ...options,
        showLoaderOnConfirm: true,
        // Toasts reject allowOutsideClick, so leave it out for them.
        ...(options.toast ? {} : {allowOutsideClick: guard(options.allowOutsideClick)}),
        allowEscapeKey: guard(options.allowEscapeKey),
        preConfirm: async (value: unknown) => {
            try {
                const response = await fetch(preConfirmUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-SweetAlert-PreConfirm': '1',
                    },
                    body: JSON.stringify({value}),
                });
                const isJson = /[/+]json\b/.test(response.headers.get('Content-Type') ?? '');
                const body = isJson ? await response.json() : null;

                if (!response.ok || response.redirected) {
                    Swal.showValidationMessage(response.redirected ? fallback : (body?.message ?? body?.detail ?? fallback));
                    return false;
                }

                // undefined keeps the user's input when the body is not JSON; a JSON `false` would keep the modal open, so it becomes null.
                return isJson ? (body === false ? null : body) : undefined;
            } catch {
                Swal.showValidationMessage(fallback);
                return false;
            }
        },
    };
}
