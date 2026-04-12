export interface NavItem {
  label: string
  href: string
  description?: string
}

export interface NavSection {
  label: string
  items: NavItem[]
}

export const siteConfig = {
  title: 'UX SweetAlert',
  description: 'Beautiful alerts, toasts, input dialogs, and Live Component workflows for Symfony.',
  basePath: '/ux-sweet-alert',
  githubUrl: 'https://github.com/pentiminax/ux-sweet-alert',
  issuesUrl: 'https://github.com/pentiminax/ux-sweet-alert/issues',
  packagistUrl: 'https://packagist.org/packages/pentiminax/ux-sweet-alert',
  ownerUrl: 'https://pentiminax.fr',
}

export function withBase(path: string): string {
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path
  }

  return `${siteConfig.basePath}${path}`
}

export const docsSidebarSections: NavSection[] = [
  {
    label: 'Getting Started',
    items: [
      { label: 'Introduction', href: withBase('/getting-started/introduction/') },
      { label: 'Installation', href: withBase('/getting-started/installation/') },
      { label: 'Quick Start', href: withBase('/getting-started/quick-start/') },
    ],
  },
  {
    label: 'Guide',
    items: [
      { label: 'Alerts', href: withBase('/guide/alerts/') },
      { label: 'Toasts', href: withBase('/guide/toasts/') },
      { label: 'Input Dialogs', href: withBase('/guide/input-dialogs/') },
      { label: 'Configuration', href: withBase('/guide/configuration/') },
      { label: 'Themes & Styling', href: withBase('/guide/themes-and-styling/') },
    ],
  },
  {
    label: 'Integrations',
    items: [
      { label: 'Live Components', href: withBase('/integrations/live-components/') },
      { label: 'Turbo', href: withBase('/integrations/turbo/') },
      { label: 'HTMX', href: withBase('/integrations/htmx/') },
    ],
  },
  {
    label: 'Reference',
    items: [
      {
        label: 'Alert Manager & Alert Object',
        href: withBase('/reference/alert-manager-and-alert-object/'),
      },
      { label: 'Input Types', href: withBase('/reference/input-types/') },
      { label: 'Result & Callbacks', href: withBase('/reference/result-and-callbacks/') },
      {
        label: 'Enums, Themes & Flash Messages',
        href: withBase('/reference/enums-themes-and-flash-messages/'),
      },
    ],
  },
]

export const docsFooterSections: NavSection[] = [
  {
    label: 'Start Here',
    items: [
      { label: 'Introduction', href: withBase('/getting-started/introduction/') },
      { label: 'Installation', href: withBase('/getting-started/installation/') },
      { label: 'Quick Start', href: withBase('/getting-started/quick-start/') },
    ],
  },
  {
    label: 'Core Workflows',
    items: [
      { label: 'Alerts', href: withBase('/guide/alerts/') },
      { label: 'Toasts', href: withBase('/guide/toasts/') },
      { label: 'Input Dialogs', href: withBase('/guide/input-dialogs/') },
      { label: 'Live Components', href: withBase('/integrations/live-components/') },
    ],
  },
  {
    label: 'Reference',
    items: [
      {
        label: 'Alert Manager & Alert Object',
        href: withBase('/reference/alert-manager-and-alert-object/'),
      },
      { label: 'Input Types', href: withBase('/reference/input-types/') },
      { label: 'Result & Callbacks', href: withBase('/reference/result-and-callbacks/') },
    ],
  },
]

export const primaryNavigation: NavItem[] = [
  { label: 'Docs', href: withBase('/getting-started/introduction/') },
  { label: 'Quick Start', href: withBase('/getting-started/quick-start/') },
  { label: 'Alerts', href: withBase('/guide/alerts/') },
  { label: 'Integrations', href: withBase('/integrations/live-components/') },
]
