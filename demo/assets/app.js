import './stimulus_bootstrap.js';
import hljs from 'highlight.js/lib/core';
import php from 'highlight.js/lib/languages/php';
import twig from 'highlight.js/lib/languages/twig';
import xml from 'highlight.js/lib/languages/xml';
import javascript from 'highlight.js/lib/languages/javascript';
import yaml from 'highlight.js/lib/languages/yaml';
import 'highlight.js/styles/github-dark.min.css';
import './styles/app.css';

for (const [name, language] of Object.entries({php, twig, xml, javascript, yaml})) {
    hljs.registerLanguage(name, language);
}

// Turbo Drive swaps the body without a full load, so highlight on every visit.
document.addEventListener('turbo:load', () => hljs.highlightAll());
