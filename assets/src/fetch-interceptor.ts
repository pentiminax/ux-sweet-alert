import {injectHiddenHtml} from './dom-utils.js';
import {ALERT_ADDED_EVENT, type AlertDetail} from './types.js';

interface HxTriggers {
    [ALERT_ADDED_EVENT]?: AlertDetail;
    [key: string]: unknown;
}

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
            if (data.alerts) {
                injectHiddenHtml(data.alerts);
            }
        }

        this.dispatchHxTriggerAlerts(response);
    }

    private dispatchHxTriggerAlerts(response: Response): void {
        const header = response.headers.get('HX-Trigger');
        if (!header) {
            return;
        }

        try {
            const triggers: HxTriggers = JSON.parse(header);
            const alertEvent = triggers[ALERT_ADDED_EVENT];
            if (alertEvent) {
                document.dispatchEvent(new CustomEvent(ALERT_ADDED_EVENT, {
                    detail: alertEvent,
                }));
            }
        } catch {
            // Not valid JSON, ignore
        }
    }
}
