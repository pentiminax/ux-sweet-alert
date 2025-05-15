import {Controller} from '@hotwired/stimulus';
import Swal from 'sweetalert2';

class default_1 extends Controller {
    constructor() {
        super(...arguments);
    }
    async connect() {
        const toasts = this.viewValue;

        console.log(toasts);

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