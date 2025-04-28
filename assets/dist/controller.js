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
            await Swal.fire(toast);
        }
    }
}


default_1.values = {
    view: Array,
};

export default default_1;