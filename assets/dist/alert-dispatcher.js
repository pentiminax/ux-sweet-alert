import Swal from 'sweetalert2';
import { BEFORE_FIRE_EVENT, AFTER_FIRE_EVENT } from './types.js';
export async function fireAlert(swalOptions, targetElement) {
    const beforeFireEvent = new CustomEvent(BEFORE_FIRE_EVENT, {
        bubbles: true,
        cancelable: true,
        detail: { swalOptions },
    });
    targetElement.dispatchEvent(beforeFireEvent);
    if (beforeFireEvent.defaultPrevented) {
        return { isConfirmed: false, isDenied: false, isDismissed: true, dismiss: Swal.DismissReason.cancel };
    }
    const result = await Swal.fire(withPreConfirm(swalOptions));
    targetElement.dispatchEvent(new CustomEvent(AFTER_FIRE_EVENT, {
        bubbles: true,
        cancelable: false,
        detail: { result, swalOptions },
    }));
    return result;
}
function withPreConfirm(swalOptions) {
    const { preConfirmUrl, preConfirmErrorMessage, ...options } = swalOptions;
    if (!preConfirmUrl) {
        return options;
    }
    const fallback = preConfirmErrorMessage ?? 'Request failed';
    const guard = (orig) => orig === false ? false : () => !Swal.isLoading() && (typeof orig === 'function' ? orig() : true);
    return {
        ...options,
        showLoaderOnConfirm: true,
        ...(options.toast ? {} : { allowOutsideClick: guard(options.allowOutsideClick) }),
        allowEscapeKey: guard(options.allowEscapeKey),
        preConfirm: async (value) => {
            try {
                const response = await fetch(preConfirmUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-SweetAlert-PreConfirm': '1',
                    },
                    body: JSON.stringify({ value }),
                });
                const isJson = /[/+]json\b/.test(response.headers.get('Content-Type') ?? '');
                const body = isJson ? await response.json() : null;
                if (!response.ok || response.redirected) {
                    Swal.showValidationMessage(response.redirected ? fallback : (body?.message ?? body?.detail ?? fallback));
                    return false;
                }
                return isJson ? (body === false ? null : body) : undefined;
            }
            catch {
                Swal.showValidationMessage(fallback);
                return false;
            }
        },
    };
}
//# sourceMappingURL=alert-dispatcher.js.map