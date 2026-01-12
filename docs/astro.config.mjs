import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';

export default defineConfig({
  site: 'https://pentiminax.github.io',
  base: '/ux-sweet-alert',
  integrations: [
    starlight({
      title: 'UX SweetAlert',
      customCss: [
        './src/styles/custom.css',
      ],
      description: 'Online documentation for the UX SweetAlert bundle.',
      sidebar: [
        {
          label: 'Getting Started',
          items: [
            { label: 'Installation', slug: 'installation' },
            { label: 'Usage', slug: 'usage' },
            { label: 'Configuration', slug: 'configuration' }
          ]
        },
        {
          label: 'Features',
          items: [
            { label: 'Alerts', slug: 'alerts' },
            { label: 'Toasts', slug: 'toasts' },
            { label: 'Live Component', slug: 'live-component' },
            { label: 'Turbo Integration', slug: 'turbo' }
          ]
        }
      ]
    })
  ]
});
