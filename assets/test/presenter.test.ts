// @vitest-environment jsdom
import {afterEach, beforeEach, describe, expect, it, vi} from 'vitest';
import Swal from 'sweetalert2';

vi.mock('sweetalert2', () => ({
    default: {
        fire: vi.fn(),
        close: vi.fn(),
        isVisible: vi.fn(() => false),
        DismissReason: {cancel: 'cancel'},
    },
}));

const action = vi.fn();
vi.mock('@symfony/ux-live-component', () => ({
    getComponent: vi.fn(async (el: HTMLElement) => ({element: el, action})),
}));

import {installNavigationGuard, present} from '../src/presenter.js';

const fire = Swal.fire as unknown as ReturnType<typeof vi.fn>;
const confirmed = {isConfirmed: true, isDenied: false, isDismissed: false, value: 'v'};

beforeEach(() => {
    fire.mockReset();
    fire.mockResolvedValue(confirmed);
    action.mockReset();
    document.body.innerHTML = '';
});

afterEach(() => {
    vi.restoreAllMocks();
    delete (window as any).myCb;
});

describe('present', () => {
    it('strips id and callbackUrl without mutating the payload', async () => {
        vi.stubGlobal('fetch', vi.fn(async () => new Response('', {headers: {'Content-Type': 'text/plain'}})));
        const payload = {title: 'x', id: 'a1', callbackUrl: '/cb'};
        await present(payload, document.body);
        expect(fire).toHaveBeenCalledWith({title: 'x'});
        expect(payload).toEqual({title: 'x', id: 'a1', callbackUrl: '/cb'});
    });

    it('runs alerts in FIFO order and survives a rejection', async () => {
        let release!: (v: unknown) => void;
        fire.mockReturnValueOnce(new Promise((_, rej) => { release = rej; }));
        const first = present({title: '1'}, document.body);
        const second = present({title: '2'}, document.body);
        await Promise.resolve();
        await Promise.resolve();
        expect(fire).toHaveBeenCalledTimes(1);
        release(new Error('boom'));
        await expect(first).rejects.toThrow('boom');
        await expect(second).resolves.toEqual(confirmed);
        expect(fire).toHaveBeenCalledTimes(2);
        expect(fire.mock.calls[1][0]).toEqual({title: '2'});
    });

    it('before-fire cancel yields dismissed result, no callback, still closed', async () => {
        const cb = vi.fn();
        (window as any).myCb = cb;
        const closed = vi.fn();
        document.addEventListener('ux-sweet-alert:z:closed', closed);
        document.body.addEventListener('sweetalert:before-fire', (e) => e.preventDefault(), {once: true});
        const result = await present({title: 'x', id: 'z'}, document.body, 'myCb');
        expect(result).toEqual({isConfirmed: false, isDenied: false, isDismissed: true, dismiss: 'cancel'});
        expect(fire).not.toHaveBeenCalled();
        expect(cb).not.toHaveBeenCalled();
        expect(closed).toHaveBeenCalledTimes(1);
    });

    it('POSTs callbackUrl, dispatches response on origin', async () => {
        const fetchMock = vi.fn(async () => new Response('{"ok":1}', {headers: {'Content-Type': 'application/json'}}));
        vi.stubGlobal('fetch', fetchMock);
        const origin = document.createElement('div');
        document.body.appendChild(origin);
        const onResponse = vi.fn();
        origin.addEventListener('ux-sweet-alert:callback:response', (e) => onResponse((e as CustomEvent).detail));
        await present({title: 'x', callbackUrl: '/cb'}, origin);
        expect(fetchMock).toHaveBeenCalledWith('/cb', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
            body: JSON.stringify({isConfirmed: true, isDenied: false, isDismissed: false, value: 'v'}),
            redirect: 'follow',
        });
        expect(onResponse).toHaveBeenCalledWith({ok: 1});
    });

    it('redirects when the callback response is redirected', async () => {
        const res = new Response('', {headers: {'Content-Type': 'text/html'}});
        Object.defineProperty(res, 'redirected', {value: true});
        Object.defineProperty(res, 'url', {value: 'http://localhost/next'});
        vi.stubGlobal('fetch', vi.fn(async () => res));
        const loc = {href: ''};
        vi.stubGlobal('location', loc);
        await present({title: 'x', callbackUrl: '/cb'}, document.body);
        expect(loc.href).toBe('http://localhost/next');
        vi.unstubAllGlobals();
    });

    it('calls a window function callback', async () => {
        const cb = vi.fn();
        (window as any).myCb = cb;
        await present({title: 'x'}, document.body, 'myCb');
        expect(cb).toHaveBeenCalledWith(confirmed);
        expect(action).not.toHaveBeenCalled();
    });

    it('uses the Live component only when origin is inside one', async () => {
        const live = document.createElement('div');
        live.setAttribute('data-controller', 'foo live');
        const inner = document.createElement('button');
        live.appendChild(inner);
        document.body.appendChild(live);
        await present({title: 'x'}, inner);
        expect(action).toHaveBeenCalledWith('callbackAction', {result: confirmed, args: {}});

        action.mockReset();
        const plain = document.createElement('div');
        document.body.appendChild(plain);
        const warn = vi.spyOn(console, 'warn');
        await present({title: 'x'}, plain);
        expect(action).not.toHaveBeenCalled();
        expect(warn).not.toHaveBeenCalled();
    });

    it('fires :closed after the callback', async () => {
        const order: string[] = [];
        (window as any).myCb = () => order.push('callback');
        document.addEventListener('ux-sweet-alert:o:closed', () => order.push('closed'));
        await present({title: 'x', id: 'o'}, document.body, 'myCb');
        expect(order).toEqual(['callback', 'closed']);
    });

    it('still fires :closed when the callback throws', async () => {
        (window as any).myCb = () => { throw new Error('boom'); };
        const error = vi.spyOn(console, 'error').mockImplementation(() => {});
        const closed = vi.fn();
        document.addEventListener('ux-sweet-alert:t:closed', closed);
        await expect(present({title: 'x', id: 't'}, document.body, 'myCb')).resolves.toEqual(confirmed);
        expect(closed).toHaveBeenCalledTimes(1);
        expect(error).toHaveBeenCalled();
    });

    it('dispatches on document when the origin left the DOM before the alert showed', async () => {
        const detached = document.createElement('div');
        const onDocument = vi.fn();
        document.addEventListener('sweetalert:before-fire', onDocument, {once: true});
        await present({title: 'x'}, detached);
        expect(onDocument).toHaveBeenCalledTimes(1);
    });
});

describe('installNavigationGuard', () => {
    it('closes a visible popup on turbo:before-render and is idempotent', () => {
        installNavigationGuard();
        installNavigationGuard();
        (Swal.isVisible as any).mockReturnValue(true);
        document.dispatchEvent(new Event('turbo:before-render'));
        expect(Swal.close).toHaveBeenCalledTimes(1);

        (Swal.close as any).mockClear();
        (Swal.isVisible as any).mockReturnValue(false);
        document.dispatchEvent(new Event('turbo:before-render'));
        expect(Swal.close).not.toHaveBeenCalled();
    });
});
