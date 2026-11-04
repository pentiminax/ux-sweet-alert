import {Controller} from '@hotwired/stimulus';
import {FetchInterceptor} from './fetch-interceptor.js';
import {installNavigationGuard, present} from './presenter.js';
import {ALERT_ADDED_EVENT, type AlertDetail, type AlertPayload} from './types.js';

export default class extends Controller {
    static values = {
        view: Array,
    };

    declare viewValue: AlertPayload[];

    private readonly fetchInterceptor = new FetchInterceptor();
    private handleAlertAdded: ((e: Event) => void) | null = null;
    private handleTurboBeforeStreamRender: ((e: Event) => void) | null = null;

    connect(): void {
        this.fetchInterceptor.install();
        installNavigationGuard();
        this.registerTurboStreamHandler();
        this.registerAlertAddedHandler();

        for (const alert of this.viewValue) {
            present(alert, this.element as HTMLElement).catch(console.error);
        }
    }

    disconnect(): void {
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

    private registerTurboStreamHandler(): void {
        if (!window.Turbo?.StreamActions || this.handleTurboBeforeStreamRender) {
            return;
        }

        this.handleTurboBeforeStreamRender = (e: Event) => {
            const detail = (e as CustomEvent).detail;
            const fallbackToDefaultActions = detail.render;

            detail.render = (streamElement: { action: string; templateContent: DocumentFragment }) => {
                if (streamElement.action !== 'alert') {
                    fallbackToDefaultActions(streamElement);
                    return;
                }

                const json = streamElement.templateContent.textContent?.trim();
                if (!json) {
                    return;
                }

                let alert: AlertPayload;
                try {
                    alert = JSON.parse(json);
                } catch {
                    console.error('Failed to parse alert JSON from Turbo stream');
                    return;
                }

                present(alert, this.element as HTMLElement).catch(console.error);
            };
        };

        document.addEventListener('turbo:before-stream-render', this.handleTurboBeforeStreamRender);
    }

    private registerAlertAddedHandler(): void {
        if (this.handleAlertAdded) {
            document.removeEventListener(ALERT_ADDED_EVENT, this.handleAlertAdded);
        }

        this.handleAlertAdded = (e: Event): void => {
            const detail = (e as CustomEvent<AlertDetail>).detail;

            for (const alert of detail.alerts ?? (detail.alert ? [detail.alert] : [])) {
                present(alert, e.target as HTMLElement, detail.callback).catch(console.error);
            }
        };

        document.addEventListener(ALERT_ADDED_EVENT, this.handleAlertAdded);
    }
}
