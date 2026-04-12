const HIDDEN_HTML_REMOVAL_DELAY = 1000;
export function injectHiddenHtml(html) {
    const container = document.createElement('div');
    container.style.display = 'none';
    container.innerHTML = html;
    document.body.appendChild(container);
    setTimeout(() => container.remove(), HIDDEN_HTML_REMOVAL_DELAY);
}
export function getLiveItemParams(element) {
    const params = {};
    for (const attr of element.attributes) {
        const match = attr.name.match(/^data-live-item-(.+)-param$/);
        if (match) {
            params[match[1]] = attr.value;
        }
    }
    return params;
}
//# sourceMappingURL=dom-utils.js.map