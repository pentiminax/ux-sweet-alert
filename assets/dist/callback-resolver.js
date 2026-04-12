import { getLiveItemParams } from './dom-utils.js';
export async function resolveCallback(callback, result, target) {
    if (typeof callback === 'string' && typeof window[callback] === 'function') {
        const fn = window[callback];
        fn(result);
        return;
    }
    try {
        const { getComponent } = await import('@symfony/ux-live-component');
        const component = await getComponent(target);
        await component.action('callbackAction', {
            result,
            args: getLiveItemParams(component.element),
        });
    }
    catch (err) {
        console.warn(err);
    }
}
//# sourceMappingURL=callback-resolver.js.map