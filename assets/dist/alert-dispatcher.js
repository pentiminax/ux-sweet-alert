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
    const result = await Swal.fire(swalOptions);
    targetElement.dispatchEvent(new CustomEvent(AFTER_FIRE_EVENT, {
        bubbles: true,
        cancelable: false,
        detail: { result, swalOptions },
    }));
    return result;
}
//# sourceMappingURL=alert-dispatcher.js.map