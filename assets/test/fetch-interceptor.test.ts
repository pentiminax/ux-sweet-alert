// @vitest-environment jsdom
import {afterEach, describe, expect, it, vi} from 'vitest';
import {FetchInterceptor} from '../src/fetch-interceptor.js';

const json = (body: string) => new Response(body, {headers: {'Content-Type': 'application/json'}});

async function run(body: string) {
    const original = vi.fn(async () => json(body));
    window.fetch = original as unknown as typeof fetch;
    const interceptor = new FetchInterceptor();
    interceptor.install();
    const added = vi.fn();
    document.addEventListener('ux-sweet-alert:alert:added', added);
    const response = await window.fetch('/x');
    interceptor.uninstall();
    document.removeEventListener('ux-sweet-alert:alert:added', added);
    return {response, added};
}

afterEach(() => vi.restoreAllMocks());

describe('FetchInterceptor', () => {
    it('does not reject on invalid JSON', async () => {
        const {response, added} = await run('not json');
        expect(response).toBeInstanceOf(Response);
        expect(added).not.toHaveBeenCalled();
    });

    it('dispatches alert:added for ux_sweet_alert', async () => {
        const {added} = await run('{"ux_sweet_alert":[{"title":"a"}]}');
        expect(added).toHaveBeenCalledTimes(1);
        expect((added.mock.calls[0][0] as CustomEvent).detail).toEqual({alerts: [{title: 'a'}]});
    });

    it('ignores other keys', async () => {
        const {added} = await run('{"alerts":[{"title":"a"}]}');
        expect(added).not.toHaveBeenCalled();
    });
});
