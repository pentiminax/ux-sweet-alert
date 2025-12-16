# Toast Notifications

The `ToastManagerInterface` provides a fluent and consistent way to display toast notifications using SweetAlert2 in Symfony applications.

## Overview

Toasts are lightweight, non-blocking notifications that appear on the screen and automatically disappear after a given time. This implementation is fully integrated with Symfony sessions and flash messages.

## Usage

Inject the `ToastManagerInterface` into your controller or service to create and add toast notifications:

```php
use Pentiminax\UX\SweetAlert\ToastManagerInterface;

public function __construct(
    private ToastManagerInterface $toastManager
) {}

public function someAction(): Response
{
    $this->toastManager->success(
        title: 'Profile Updated!',
        text: 'Your changes have been saved.',
        timer: 3000,
        timerProgressBar: true
    );

    return $this->redirectToRoute('profile');
}
```

## Themes

Toasts share the same theme API as alerts:

```php
use Pentiminax\UX\SweetAlert\Enum\Theme;

$toast = $this->toastManager->success(title: 'Saved!');
$toast->theme(Theme::MaterialUILight);
```

Theme styles are not auto-imported (see `assets/package.json` `autoimport` entries).
