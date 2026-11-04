import {injectHiddenHtml} from './dom-utils.js';
import {ALERT_ADDED_EVENT, JSON_KEY} from './types.js';

export class FetchInterceptor {
    private originalFetch: typeof window.fetch | null = null;

    install(): void {
        if (this.originalFetch) {
            return;
        }

        this.originalFetch = window.fetch;
        window.fetch = this.intercept.bind(this);
    }

    uninstall(): void {
        if (!this.originalFetch) {
            return;
        }

        window.fetch = this.originalFetch;
        this.originalFetch = null;
    }

    private async intercept(...args: Parameters<typeof fetch>): Promise<Response> {
        const response = await this.originalFetch!.apply(window, args);
        await this.extractAlerts(response);
        return response;
    }

    private async extractAlerts(response: Response): Promise<void> {
        const contentType = response.headers.get('Content-Type') ?? '';

        if (contentType.includes('text/vnd.turbo-stream.html')) {
            const html = await response.clone().text();
            injectHiddenHtml(html);
            return;
        }

        if (contentType.includes('application/json')) {
            const data = await response.clone().json();
            const alerts = data !== null && typeof data === 'object' ? data[JSON_KEY] : undefined;
            if (Array.isArray(alerts)) {
                document.dispatchEvent(new CustomEvent(ALERT_ADDED_EVENT, {
                    detail: {alerts},
                }));
            }
        }
    }
}
