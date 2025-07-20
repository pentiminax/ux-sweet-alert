# Turbo integration

## Overview

UX SweetAlert provides interactive popups for your Symfony applications.
With built-in UX Turbo integration, alerts work seamlessly both on full page reloads and with Turbo-driven navigation or
AJAX/fetch requests.

## Requirements

- Symfony UX Turbo

```bash
composer require symfony/ux-turbo
```

## Alerts are automatically injected

On the next Turbo navigation, page reload, or AJAX response returning Turbo Stream, the bundle will automatically inject
a <turbo-stream action="SweetAlert"> with the alert configuration.

You do not need to manage anything on the frontend:
Your custom SweetAlert action will be executed and the popup will appear.

## Using with fetch / AJAX requests

Even when using fetch directly, no extra adaptation is required:

```javascript
await fetch('/your-endpoint');
```

The bundle will automatically intercept Turbo Stream responses containing a SweetAlert alert, inject it into the DOM,
and trigger the popup—no page reload required.

## Backend example

```php
use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Symfony\Component\HttpFoundation\Response;

public function index(AlertManagerInterface $alertManager): Response
{
    $this->alertManager->info(
        id: 'alertId',
        title: 'Alert Title',
    );

    return $this->json([
        'data' => '...'
    ]);
}
```

The bundle will automatically add an alerts key to your JSON response, which will contain the Turbo Stream payload for
the alert.

## Example response:

```json
{
  "data": "...",
  "alerts": "<turbo-stream action=\"alert\"><template>{\"id\":\"alertId\",\"title\":\"alertTitle\"}</template></turbo-stream>"
}
```

You do not need to return a Turbo Stream response manually. The bundle takes care of it for you.