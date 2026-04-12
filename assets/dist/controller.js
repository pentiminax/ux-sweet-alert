import {Controller} from '@hotwired/stimulus';
import Swal from 'sweetalert2';
import {getComponent} from '@symfony/ux-live-component';

class default_1 extends Controller {
    constructor() {
        super(...arguments);
        this.handleAlertAdded = null;
        this.handleTurboBeforeStreamRender = null;
    }

    async connect() {
        if (window.Turbo && window.Turbo.StreamActions && !this.handleTurboBeforeStreamRender) {
            this.originalFetch = window.fetch;
            window.fetch = this.fetch.bind(this);

            this.handleTurboBeforeStreamRender = ((e) => {
                const fallbackToDefaultActions = e.detail.render;

                e.detail.render = async function (streamElement) {
                    if (streamElement.action === 'alert') {
                        const json = streamElement.templateContent.textContent.trim();

                        if (!json) {
                            return;
                        }

                        let alert;
                        try {
                            alert = JSON.parse(json);
                        } catch (e) {
                            console.error('Failed to parse JSON:', e);
                            return;
                        }
                        const alertId = alert.id;

                        delete alert.id;

                        const result = await this.fireAlert(alert, this.element);

                        document.dispatchEvent(new CustomEvent(`ux-sweet-alert:${alertId}:closed`, {
                            detail: result
                        }));
                    } else {
                        fallbackToDefaultActions(streamElement)
                    }
                }.bind(this);
            });

            document.addEventListener('turbo:before-stream-render', this.handleTurboBeforeStreamRender);
        }

        if (this.handleAlertAdded) {
            document.removeEventListener('ux-sweet-alert:alert:added', this.handleAlertAdded);
        }

        this.handleAlertAdded = async (e) => {
            const alert = e.detail['alert'];

            if (alert.id) {
                delete alert.id;
            }

            const result = await this.fireAlert(alert, e.target);

            let callback = e.detail['callback'] ?? null;

            if (typeof callback === 'string' && typeof window[callback] === 'function') {
                callback = window[callback];

                if (typeof callback === 'function') {
                    callback(result);
                }
            } else {
                try {
                    const component = await getComponent(e.target);
                    component.action('callbackAction', {
                        'result': result,
                        'args': this.getLiveItemParams(component.element)
                    })
                } catch (e) {
                    console.warn(e);
                }
            }
        };

        document.addEventListener('ux-sweet-alert:alert:added', this.handleAlertAdded);

        const toasts = this.viewValue;
        for (const toast of toasts) {
            const toastId = toast.id;
            const callbackUrl = toast.callbackUrl ?? null;

            const swalOptions = Object.assign({}, toast);
            delete swalOptions.callbackUrl;

            const result = await this.fireAlert(swalOptions, this.element);

            if (callbackUrl) {
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
                    } else if (response.headers.get('Content-Type')?.includes('application/json')) {
                        const data = await response.json();
                        this.element.dispatchEvent(new CustomEvent('ux-sweet-alert:callback:response', {
                            bubbles: true,
                            detail: data,
                        }));
                    }
                } catch (e) {
                    console.warn('[ux-sweet-alert] Callback URL request failed:', e);
                }
            } else {
                document.dispatchEvent(new CustomEvent(`ux-sweet-alert:${toastId}:closed`, {
                    detail: result
                }));
            }
        }
    }

    async fireAlert(swalOptions, targetElement) {
        const beforeFireEvent = new CustomEvent('sweetalert:before-fire', {
            bubbles: true,
            cancelable: true,
            detail: { swalOptions },
        });

        targetElement.dispatchEvent(beforeFireEvent);

        if (beforeFireEvent.defaultPrevented) {
            return { isConfirmed: false, isDenied: false, isDismissed: true, dismiss: 'cancel' };
        }

        const result = await Swal.fire(swalOptions);

        const afterFireEvent = new CustomEvent('sweetalert:after-fire', {
            bubbles: true,
            cancelable: false,
            detail: { result, swalOptions },
        });

        targetElement.dispatchEvent(afterFireEvent);

        return result;
    }

    disconnect() {
        if (this.handleAlertAdded) {
            document.removeEventListener('ux-sweet-alert:alert:added', this.handleAlertAdded);
            this.handleAlertAdded = null;
        }

        if (this.handleTurboBeforeStreamRender) {
            document.removeEventListener('turbo:before-stream-render', this.handleTurboBeforeStreamRender);
            this.handleTurboBeforeStreamRender = null;
        }

        if (this.originalFetch) {
            window.fetch = this.originalFetch;
            this.originalFetch = null;
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

    getLiveItemParams(element) {
        const params = {};

        for (const attr of element.attributes) {
            const match = attr.name.match(/^data-live-item-(.+)-param$/);
            if (match) {
                params[match[1]] = attr.value;
            }
        }

        return params;
    }
}


default_1.values = {
    view: Array,
};

export default default_1;
