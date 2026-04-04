# UX SweetAlert

[![Latest Stable Version](https://img.shields.io/packagist/v/pentiminax/ux-sweet-alert.svg?style=flat-square)](https://packagist.org/packages/pentiminax/ux-sweet-alert)
[![PHP Version](https://img.shields.io/packagist/php-v/pentiminax/ux-sweet-alert?style=flat-square)](https://packagist.org/packages/pentiminax/ux-sweet-alert)
[![Downloads total](https://img.shields.io/packagist/dt/pentiminax/ux-sweet-alert.svg?style=flat-square)](https://packagist.org/packages/pentiminax/ux-sweet-alert/stats)
[![Coverage](https://img.shields.io/codecov/c/github/pentiminax/ux-sweet-alert?style=flat-square)](https://codecov.io/gh/pentiminax/ux-sweet-alert)

UX SweetAlert is a Symfony bundle that integrates the SweetAlert2 library into your Symfony applications. It provides PHP helpers, input-type abstractions, Live Components, and a Stimulus controller to display alerts, input dialogs, and toast notifications.

## Requirements

- PHP 8.3 or higher
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

#[Route('/', name: 'app_homepage')]
public function index(AlertManagerInterface $alertManager): Response
{
    $alertManager->success(
        title: 'Update Successful',
        text: 'Your settings have been saved.'
    );

    return $this->redirectToRoute('dashboard');
}
```

## Toasts

Use the `AlertManagerInterface` service with the `toast()` method to create toast notifications:

```php
use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\Enum\Position;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(AlertManagerInterface $alertManager): Response
    {
       $alertManager->toast(
            title: 'title',
            text: 'text',
            position: Position::TOP_END,
            timer: 3000,
            timerProgressBar: true
        );

        return $this->render('home/index.html.twig');
    }
}
```

## Input dialogs

Use `AlertManagerInterface::input()` with one of the provided input type classes when you want to collect user input from PHP:

```php
use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\InputType\Text;

#[Route('/profile', name: 'app_profile')]
public function index(AlertManagerInterface $alertManager): Response
{
    $alertManager->input(
        inputType: new Text(
            label: 'Display name',
            value: 'Tanguy',
            placeholder: 'Enter your display name',
        ),
        title: 'Update profile',
        text: 'This change is visible to other users.'
    );

    return $this->render('profile/index.html.twig');
}
```

Available helpers include `Text`, `Textarea`, `Select`, `Radio`, `Checkbox`, `File`, `Range`, and `HtmlInputType` for other native HTML input types.

## Live Components

The bundle also ships `SweetAlert:ConfirmButton` and `SweetAlert:InputModal` Live Components. Render `{{ ux_sweet_alert_scripts() }}` on the page, then use the component that matches your interaction pattern. See the online documentation for full examples.

## Documentation

- [Online documentation](https://pentiminax.github.io/ux-sweet-alert/)
