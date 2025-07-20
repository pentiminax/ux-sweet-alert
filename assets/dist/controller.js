import {Controller} from '@hotwired/stimulus';
import Swal from 'sweetalert2';

class default_1 extends Controller {
    constructor() {
        super(...arguments);
    }

    initialize() {
        if (window.Turbo && window.Turbo.StreamActions) {
            this.originalFetch = window.fetch;
            window.fetch = this.fetch.bind(this);

            document.addEventListener('turbo:before-stream-render', ((e) => {
                const fallbackToDefaultActions = e.detail.render;

                e.detail.render = async function (streamElement) {
                    if (streamElement.action === 'alert') {
                        const json = streamElement.templateContent.textContent.trim();

                        if (!json) {
                            return;
                        }

                        const alert = JSON.parse(json);
                        const alertId = alert.id;

                        delete alert.id;

                        const result = await Swal.fire(alert);

                        document.dispatchEvent(new CustomEvent(`ux-sweet-alert:${alertId}:closed`, {
                            detail: result
                        }));
                    } else {
                        fallbackToDefaultActions(streamElement)
                    }
                }
            }));
        }
    }

    async connect() {
        const toasts = this.viewValue;

        document.addEventListener('ux-sweet-alert:alert:added', async (e) => {
            const alert = e.detail['alert'];

            const result = await Swal.fire(alert);
            delete alert.id;

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
            }));
        }
    }

    async fetch(...args) {
        const response = await this.originalFetch.apply(window, args);
        await this.handleTurboStreams(response);
        return response;
    }

    async handleTurboStreams(response) {
        const contentType = response.headers.get('Content-Type') || '';

        if (contentType.includes('text/vnd.turbo-stream.html')) {
            const html = await response.clone().text();
            this.injectHiddenHtml(html);

            return;
        }

        if (contentType.includes('application/json')) {
            const data = await response.clone().json();
            if (data.alerts) {
                this.injectHiddenHtml(data.alerts);
            }
        }
    }

    injectHiddenHtml(html) {
        const tempDiv = document.createElement('div');
        tempDiv.style.display = 'none';
        tempDiv.innerHTML = html;

        document.body.appendChild(tempDiv);

        setTimeout(function () {
            tempDiv.remove();
        }, 1000);
    }
}


default_1.values = {
    view: Array,
};

export default default_1;