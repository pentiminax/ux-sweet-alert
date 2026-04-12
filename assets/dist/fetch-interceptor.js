import { injectHiddenHtml } from './dom-utils.js';
import { ALERT_ADDED_EVENT } from './types.js';
export class FetchInterceptor {
    constructor() {
        this.originalFetch = null;
    }
    install() {
        if (this.originalFetch) {
            return;
        }
        this.originalFetch = window.fetch;
        window.fetch = this.intercept.bind(this);
    }
    uninstall() {
        if (!this.originalFetch) {
            return;
        }
        window.fetch = this.originalFetch;
        this.originalFetch = null;
    }
    async intercept(...args) {
        const response = await this.originalFetch.apply(window, args);
        await this.extractAlerts(response);
        return response;
    }
    async extractAlerts(response) {
        const contentType = response.headers.get('Content-Type') ?? '';
        if (contentType.includes('text/vnd.turbo-stream.html')) {
            const html = await response.clone().text();
            injectHiddenHtml(html);
            return;
        }
        if (contentType.includes('application/json')) {
            const data = await response.clone().json();
            if (data.alerts) {
                injectHiddenHtml(data.alerts);
            }
        }
        this.dispatchHxTriggerAlerts(response);
    }
    dispatchHxTriggerAlerts(response) {
        const header = response.headers.get('HX-Trigger');
        if (!header) {
            return;
        }
        try {
            const triggers = JSON.parse(header);
            const alertEvent = triggers[ALERT_ADDED_EVENT];
            if (alertEvent) {
                document.dispatchEvent(new CustomEvent(ALERT_ADDED_EVENT, {
                    detail: alertEvent,
                }));
            }
        }
        catch {
        }
    }
}
//# sourceMappingURL=fetch-interceptor.js.map