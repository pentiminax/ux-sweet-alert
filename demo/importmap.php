<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@hotwired/turbo' => [
        'version' => '8.0.23',
    ],
    'sweetalert2' => [
        'version' => '11.26.25',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@symfony/ux-live-component' => [
        'path' => './vendor/symfony/ux-live-component/assets/dist/live_controller.js',
    ],
    'highlight.js/lib/core' => [
        'version' => '11.12.0',
    ],
    'highlight.js/lib/languages/php' => [
        'version' => '11.12.0',
    ],
    'highlight.js/lib/languages/twig' => [
        'version' => '11.12.0',
    ],
    'highlight.js/lib/languages/yaml' => [
        'version' => '11.12.0',
    ],
    'highlight.js/styles/github-dark.min.css' => [
        'version' => '11.12.0',
        'type' => 'css',
    ],
    'highlight.js/lib/languages/xml' => [
        'version' => '11.12.0',
    ],
    'highlight.js/lib/languages/javascript' => [
        'version' => '11.12.0',
    ],
];
