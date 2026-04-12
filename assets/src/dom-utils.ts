const HIDDEN_HTML_REMOVAL_DELAY = 1000;

export function injectHiddenHtml(html: string): void {
    const container = document.createElement('div');
    container.style.display = 'none';
    container.innerHTML = html;

    document.body.appendChild(container);

    setTimeout(() => container.remove(), HIDDEN_HTML_REMOVAL_DELAY);
}

export function getLiveItemParams(element: HTMLElement): Record<string, string> {
    const params: Record<string, string> = {};

    for (const attr of element.attributes) {
        const match = attr.name.match(/^data-live-item-(.+)-param$/);
        if (match) {
            params[match[1]] = attr.value;
        }
    }

    return params;
}
