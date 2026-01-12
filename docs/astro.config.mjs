import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';

export default defineConfig({
  site: 'https://pentiminax.github.io',
  base: '/ux-sweet-alert',
  integrations: [
    starlight({
      title: 'UX SweetAlert',
      description: 'Documentation du bundle UX SweetAlert.',
      sidebar: [
        {
          label: 'Prise en main',
          items: [
            { label: 'Installation', link: '/installation/' },
            { label: 'Utilisation', link: '/usage/' },
            { label: 'Configuration', link: '/configuration/' }
          ]
        },
        {
          label: 'Fonctionnalités',
          items: [
            { label: 'Alertes', link: '/alerts/' },
            { label: 'Toasts', link: '/toasts/' },
            { label: 'Live Component', link: '/live-component/' },
            { label: 'Intégration Turbo', link: '/turbo/' }
          ]
        }
      ]
    })
  ]
});
