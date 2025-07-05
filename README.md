# UX SweetAlert

UX SweetAlert is a Symfony bundle that integrates the SweetAlert2 library into your Symfony applications. It provides PHP helpers and a Stimulus controller to easily display alerts and toast notifications.

## Requirements

- PHP 8.2 or higher
- Symfony StimulusBundle
- Composer

## Installation

Install the library via Composer:

```bash
composer require pentiminax/ux-sweet-alert
```

## Basic usage

To automatically display toasts and alerts in your templates, add the following Twig function in your base.html.twig (or the layout file):

```twig
{{ ux_sweet_alert_scripts() }}
```

## Alerts

Inject the AlertManagerInterface and use the helper methods to create alerts:

```php
use Pentiminax\UX\SweetAlert\AlertManagerInterface;

public function someAction(AlertManagerInterface $alertManager): Response
{

    $alertManager->success(
        id: 'update-success',
        title: 'Update Successful',
        text: 'Your settings have been saved.'
    );

    return $this->redirectToRoute('dashboard');
}
```

## Toasts

Inject the `ToastManagerInterface` service and
create toasts:

```php
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\ToastManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(ToastManagerInterface $toastManager): Response
    {
       $toastManager->success(
            id: 'id',
            title: 'title',
            text: 'text',
            position: Position::TOP_END,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        );

        return $this->render('home/index.html.twig');
    }
}
```

## Advanced documentation

- [Installation](https://github.com/pentiminax/ux-sweet-alert/blob/main/docs/installation.md)
- [Usage](https://github.com/pentiminax/ux-sweet-alert/blob/main/docs/usage.md)
- [Alerts](https://github.com/pentiminax/ux-sweet-alert/blob/main/docs/alerts.md)
- [Toasts](https://github.com/pentiminax/ux-sweet-alert/blob/main/docs/toasts.md)
- [Live Component](https://github.com/pentiminax/ux-sweet-alert/blob/main/docs/live-component.md)

