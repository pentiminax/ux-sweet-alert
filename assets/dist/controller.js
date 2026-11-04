import { Controller } from '@hotwired/stimulus';
import { FetchInterceptor } from './fetch-interceptor.js';
import { installNavigationGuard, present } from './presenter.js';
import { ALERT_ADDED_EVENT } from './types.js';
class default_1 extends Controller {
    constructor() {
        super(...arguments);
        this.fetchInterceptor = new FetchInterceptor();
        this.handleAlertAdded = null;
        this.handleTurboBeforeStreamRender = null;
    }
    connect() {
        this.fetchInterceptor.install();
        installNavigationGuard();
        this.registerTurboStreamHandler();
        this.registerAlertAddedHandler();
        for (const alert of this.viewValue) {
            present(alert, this.element).catch(console.error);
        }
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
            detail.render = (streamElement) => {
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
                present(alert, this.element).catch(console.error);
            };
        };
        document.addEventListener('turbo:before-stream-render', this.handleTurboBeforeStreamRender);
    }
    registerAlertAddedHandler() {
        if (this.handleAlertAdded) {
            document.removeEventListener(ALERT_ADDED_EVENT, this.handleAlertAdded);
        }
        this.handleAlertAdded = (e) => {
            const detail = e.detail;
            for (const alert of detail.alerts ?? (detail.alert ? [detail.alert] : [])) {
                present(alert, e.target, detail.callback).catch(console.error);
            }
        };
        document.addEventListener(ALERT_ADDED_EVENT, this.handleAlertAdded);
    }
}
default_1.values = {
    view: Array,
};
export default default_1;
//# sourceMappingURL=controller.js.map