import {Controller} from '@hotwired/stimulus';
import type {SweetAlertOptions} from 'sweetalert2';
import {fireAlert} from './alert-dispatcher.js';
import {resolveCallback} from './callback-resolver.js';
import {FetchInterceptor} from './fetch-interceptor.js';
import {
    ALERT_ADDED_EVENT,
    ALERT_CLOSED_EVENT_PREFIX,
    CALLBACK_RESPONSE_EVENT,
    type AlertDetail,
    type ToastValue,
} from './types.js';

export default class extends Controller {
    static values = {
        view: Array,
    };

    declare viewValue: ToastValue[];

    private readonly fetchInterceptor = new FetchInterceptor();
    private handleAlertAdded: ((e: Event) => Promise<void>) | null = null;
    private handleTurboBeforeStreamRender: ((e: Event) => void) | null = null;

    async connect(): Promise<void> {
        this.fetchInterceptor.install();
        this.registerTurboStreamHandler();
        this.registerAlertAddedHandler();
        await this.processInitialToasts();
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

            detail.render = async (streamElement: { action: string; templateContent: DocumentFragment }) => {
                if (streamElement.action !== 'alert') {
                    fallbackToDefaultActions(streamElement);
                    return;
                }

                const json = streamElement.templateContent.textContent?.trim();
                if (!json) {
                    return;
                }

                let alert: SweetAlertOptions & { id?: string };
                try {
                    alert = JSON.parse(json);
                } catch {
                    console.error('Failed to parse alert JSON from Turbo stream');
                    return;
                }

                const alertId = alert.id;
                delete alert.id;

                const result = await fireAlert(alert, this.element as HTMLElement);

                document.dispatchEvent(new CustomEvent(`${ALERT_CLOSED_EVENT_PREFIX}${alertId}:closed`, {
                    detail: result,
                }));
            };
        };

        document.addEventListener('turbo:before-stream-render', this.handleTurboBeforeStreamRender);
    }

    private registerAlertAddedHandler(): void {
        if (this.handleAlertAdded) {
            document.removeEventListener(ALERT_ADDED_EVENT, this.handleAlertAdded);
        }

        this.handleAlertAdded = async (e: Event): Promise<void> => {
            const detail = (e as CustomEvent<AlertDetail>).detail;
            const alert = detail.alert;

            if (alert.id) {
                delete alert.id;
            }

            const result = await fireAlert(alert, e.target as HTMLElement);
            await resolveCallback(detail.callback, result, e.target as HTMLElement);
        };

        document.addEventListener(ALERT_ADDED_EVENT, this.handleAlertAdded);
    }

    private async processInitialToasts(): Promise<void> {
        for (const toast of this.viewValue) {
            const toastId = toast.id;
            const callbackUrl = toast.callbackUrl ?? null;

            const swalOptions: SweetAlertOptions = {...toast};
            delete (swalOptions as ToastValue).callbackUrl;
            delete (swalOptions as ToastValue).id;

            const result = await fireAlert(swalOptions, this.element as HTMLElement);

            if (callbackUrl) {
                await this.postCallbackResult(callbackUrl, result);
            } else {
                document.dispatchEvent(new CustomEvent(`${ALERT_CLOSED_EVENT_PREFIX}${toastId}:closed`, {
                    detail: result,
                }));
            }
        }
    }

    private async postCallbackResult(callbackUrl: string, result: { isConfirmed: boolean; isDenied: boolean; isDismissed: boolean; value?: unknown }): Promise<void> {
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
        } catch (err) {
            console.warn('[ux-sweet-alert] Callback URL request failed:', err);
        }
    }
}
