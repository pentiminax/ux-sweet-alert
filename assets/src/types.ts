import type {SweetAlertOptions} from 'sweetalert2';

export interface AlertDetail {
    alert: SweetAlertOptions & { id?: string };
    callback?: string;
}

export type ToastValue = SweetAlertOptions & {
    id?: string;
    callbackUrl?: string;
};

export const ALERT_ADDED_EVENT = 'ux-sweet-alert:alert:added';
export const ALERT_CLOSED_EVENT_PREFIX = 'ux-sweet-alert:';
export const CALLBACK_RESPONSE_EVENT = 'ux-sweet-alert:callback:response';
export const BEFORE_FIRE_EVENT = 'sweetalert:before-fire';
export const AFTER_FIRE_EVENT = 'sweetalert:after-fire';

declare global {
    interface Window {
        Turbo?: {
            StreamActions?: Record<string, unknown>;
        };
        [key: string]: unknown;
    }
}
