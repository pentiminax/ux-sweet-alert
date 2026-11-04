import { Controller } from '@hotwired/stimulus';
import { fireAlert } from './alert-dispatcher.js';
import { resolveCallback } from './callback-resolver.js';
import { FetchInterceptor } from './fetch-interceptor.js';
import { ALERT_ADDED_EVENT, ALERT_CLOSED_EVENT_PREFIX, CALLBACK_RESPONSE_EVENT, } from './types.js';
class default_1 extends Controller {
    constructor() {
        super(...arguments);
        this.fetchInterceptor = new FetchInterceptor();
        this.handleAlertAdded = null;
        this.handleTurboBeforeStreamRender = null;
    }
    async connect() {
        this.fetchInterceptor.install();
        this.registerTurboStreamHandler();
        this.registerAlertAddedHandler();
        await this.processInitialToasts();
    }
    disconnect() {
        if (this.handleAlertAdded) {
            document.removeEventListener(ALERT_ADDED_EVENT, this.handleAlertAdded);
            this.handleAlertAdded = null;
        }
        if (this.handleTurboBeforeStreamRender) {
            document.removeEventListener('turbo:before-stream-render', this.handleTurboBeforeStreamRender);
            this.handleTurboBeforeStreamRender = null;
        }
        this.fetchInterceptor.uninstall();
    }
    registerTurboStreamHandler() {
        if (!window.Turbo?.StreamActions || this.handleTurboBeforeStreamRender) {
            return;
        }
        this.handleTurboBeforeStreamRender = (e) => {
            const detail = e.detail;
            const fallbackToDefaultActions = detail.render;
            detail.render = async (streamElement) => {
                if (streamElement.action !== 'alert') {
                    fallbackToDefaultActions(streamElement);
                    return;
                }
                const json = streamElement.templateContent.textContent?.trim();
                if (!json) {
                    return;
                }
                let alert;
                try {
                    alert = JSON.parse(json);
                }
                catch {
                    console.error('Failed to parse alert JSON from Turbo stream');
                    return;
                }
                const alertId = alert.id;
                delete alert.id;
                const result = await fireAlert(alert, this.element);
                document.dispatchEvent(new CustomEvent(`${ALERT_CLOSED_EVENT_PREFIX}${alertId}:closed`, {
                    detail: result,
                }));
            };
        };
        document.addEventListener('turbo:before-stream-render', this.handleTurboBeforeStreamRender);
    }
    registerAlertAddedHandler() {
        if (this.handleAlertAdded) {
            document.removeEventListener(ALERT_ADDED_EVENT, this.handleAlertAdded);
        }
        this.handleAlertAdded = async (e) => {
            const detail = e.detail;
            for (const { id, ...alert } of detail.alerts ?? (detail.alert ? [detail.alert] : [])) {
                const result = await fireAlert(alert, e.target);
                await resolveCallback(detail.callback, result, e.target);
                if (id) {
                    document.dispatchEvent(new CustomEvent(`${ALERT_CLOSED_EVENT_PREFIX}${id}:closed`, {
                        detail: result,
                    }));
                }
            }
        };
        document.addEventListener(ALERT_ADDED_EVENT, this.handleAlertAdded);
    }
    async processInitialToasts() {
        for (const toast of this.viewValue) {
            const toastId = toast.id;
            const callbackUrl = toast.callbackUrl ?? null;
            const swalOptions = { ...toast };
            delete swalOptions.callbackUrl;
            delete swalOptions.id;
            const result = await fireAlert(swalOptions, this.element);
            if (callbackUrl) {
                await this.postCallbackResult(callbackUrl, result);
            }
            else {
                document.dispatchEvent(new CustomEvent(`${ALERT_CLOSED_EVENT_PREFIX}${toastId}:closed`, {
                    detail: result,
                }));
            }
        }
    }
    async postCallbackResult(callbackUrl, result) {
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
            this.element.dispatchEvent(new CustomEvent(CALLBACK_RESPONSE_EVENT, {
                bubbles: true,
                detail,
            }));
        }
        catch (err) {
            console.warn('[ux-sweet-alert] Callback URL request failed:', err);
        }
    }
}
default_1.values = {
    view: Array,
};
export default default_1;
//# sourceMappingURL=controller.js.map