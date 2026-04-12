import type {SweetAlertResult} from 'sweetalert2';
import {getLiveItemParams} from './dom-utils.js';

export async function resolveCallback(
    callback: string | undefined | null,
    result: SweetAlertResult,
    target: HTMLElement,
): Promise<void> {
    if (typeof callback === 'string' && typeof window[callback] === 'function') {
        const fn = window[callback] as (result: SweetAlertResult) => void;
        fn(result);
        return;
    }

    try {
        const {getComponent} = await import('@symfony/ux-live-component');
        const component = await getComponent(target);
        await component.action('callbackAction', {
            result,
            args: getLiveItemParams(component.element),
        });
    } catch (err) {
        console.warn(err);
    }
}
