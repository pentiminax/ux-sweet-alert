# Alerts

The AlertManagerInterface provides a structured and expressive way to trigger SweetAlert2 modal alerts within your Symfony application.

## Overview

Alerts are modal dialogs used to notify users or require confirmation. Unlike toasts, alerts are blocking interactions and typically require user action (e.g., confirmation or cancellation).

This library leverages Symfony’s session flash system to store alerts and renders them on the next page load.

## Usage

Inject the AlertManagerInterface and use the helper methods to create alerts:

```php
use Pentiminax\UX\SweetAlert\AlertManagerInterface;

public function someAction(AlertManagerInterface $alertManager): Response
{

    $alertManager->success(
        title: 'Update Successful',
        text: 'Your settings have been saved.'
    );

    return $this->redirectToRoute('dashboard');
}
```

## Alert Types

The following predefined alert types are available:
-	success()
-	error()
-	warning()
-	info()
-	question()

Each method returns an Alert object that can be further customized using a fluent API.

## Parameters

| Name       | Type            | Default            | Description              |
|------------|-----------------|--------------------|--------------------------|
| `id`       | `string`        | *(required)*       | Unique identifier        |
| `title`    | `string`        | *(required)*       | Alert title              |
| `text`     | `string`        | `''`               | Description text         |
| `position` | `Position` enum | `Position::CENTER` | Modal position on screen |

## Customization

After creating an alert, you can customize its behavior:

```php
$alert = $this->alertManager->info(
    title: 'Info Alert',
    text: 'This is an info alert',
);
        
$alert
    ->withCancelButton()
    ->withDenyButton();
```

## Available Customization Methods

| Method                        | Description                                          |
|-------------------------------|------------------------------------------------------|
| `withCancelButton()`          | Shows a cancel button                                |
| `withDenyButton()`            | Shows a deny button                                  |
| `withoutConfirmButton()`      | Hides the confirm button                             |
| `withoutAnimation()`          | Disables animation                                   |
| `withoutBackdrop()`           | Removes modal backdrop                               |
| `theme(Theme $theme)`         | Sets the theme (default `auto`)                      |
| `confirmButtonColor(string)`  | Sets confirm button color (hex value)                |
| `denyOutsideClick()`          | Prevents closing the modal by clicking outside       |
| `denyEscapeKey()`             | Prevents closing the modal with the ESC key          |

## Themes

SweetAlert2 ships several themes. Set them directly from PHP:

```php
$alert
    ->theme(Theme::Auto)
    ->theme(Theme::Dark)
    ->theme(Theme::Light)
    ->theme(Theme::Borderless)
    ->theme(Theme::Bootstrap5)
    ->theme(Theme::Bootstrap4)
    ->theme(Theme::MaterialUI)
;
```

The optional SweetAlert2 theme styles are not auto-imported. The `autoimport` section in `assets/package.json` disables them by default so you can choose the right stylesheet yourself:

```json
"autoimport": {
  "sweetalert2/themes/bootstrap-4.css": false,
  "sweetalert2/themes/bootstrap-5.css": false,
  "sweetalert2/themes/bulma.css": false,
  "sweetalert2/themes/material-ui.css": false
}
```

## Retrieving Alerts

To access alerts:

```php
$alerts = $alertManager->getAlerts();
```

## Notes
- 	Alerts are stored in Symfony’s FlashBag and persist for one request.
- 	Alerts must be rendered on the frontend with SweetAlert2-compatible JavaScript.

⸻

Build clear and engaging user interactions with powerful alerts
