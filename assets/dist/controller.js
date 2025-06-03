import {Controller} from '@hotwired/stimulus';
import Swal from 'sweetalert2';

class default_1 extends Controller {
    constructor() {
        super(...arguments);
    }
    async connect() {
        const toasts = this.viewValue;

        document.addEventListener('ux-sweet-alert:alert:added', async(e) => {
            const alert = e.detail['alert'];

            const result = await Swal.fire(alert);

            let callback = e.detail['callback'] ?? null;

            if (typeof callback === 'string' && typeof window[callback] === 'function') {
                callback = window[callback];
            }

            if (typeof callback === 'function') {
                callback(result);
            }
        });

        for (const toast of toasts) {
            const result = await Swal.fire(toast);

            document.dispatchEvent(new CustomEvent(`ux-sweet-alert:${toast.id}:closed`, {
                detail: result
            }))
        }
    }
}


default_1.values = {
    view: Array,
};

export default default_1;