import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';

export default defineConfig({
  site: 'https://pentiminax.github.io',
  base: import.meta.env.PROD ? '/ux-sweet-alert' : '/',
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
            { label: 'Installation', link: '/installation/' },
            { label: 'Usage', link: '/usage/' },
            { label: 'Configuration', link: '/configuration/' }
          ]
        },
        {
          label: 'Features',
          items: [
            { label: 'Alerts', link: '/alerts/' },
            { label: 'Toasts', link: '/toasts/' },
            { label: 'Live Component', link: '/live-component/' },
            { label: 'Turbo Integration', link: '/turbo/' }
          ]
        }
      ]
    })
  ]
});
