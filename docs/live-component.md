# 🔘 Live Component

## Confirm Button

The `ConfirmButton` Live Component lets you trigger a **SweetAlert2 confirmation dialog** when clicking a button. You
can customize the title, text, icon, and the follow-up action via a callback.

### ✅ Requirements

* Your frontend must listen to the `ux-sweet-alert:alert:added` event and handle it with SweetAlert2

---

### 🔍 Usage Example

```twig
<twig:SweetAlert:ConfirmButton
    title="Are you sure?"
    text="This action cannot be undone."
    icon="warning"
    showCancelButton="true"
    callback="onConfirmAction"
/>

```

---

### 📃 Available Props

| Name               | Type   | Description                                                      |
|--------------------|--------|------------------------------------------------------------------|
| `title`            | string | Title displayed in the confirmation dialog                       |
| `text`             | string | Additional description text                                      |
| `icon`             | string | One of: `success`, `error`, `warning`, `info`, `question`        |
| `showCancelButton` | bool   | Whether to show the "Cancel" button (default: `false`)           |
| `callback`         | string | Live component method name to invoke if user confirms the dialog |

---

### 📊 Behavior

1. Button triggers a `live#emit` event with the `alertAdded` identifier.
2. The LiveComponent method `alertAdded()` dispatches the JS event `ux-sweet-alert:alert:added`.
3. Your frontend JS listens to this event and calls `Swal.fire(...)`.
4. On confirmation, the `callback` method is invoked using LiveComponent.

---

### 🔮 Example Usage

```html

<div class="container">
    <div class="row">
        <div class="col">
            <twig:SweetAlert:ConfirmButton
                    title="Confirm"
                    text="Are you sure?"
                    callback="myCallback"
            />
        </div>
    </div>
</div>
<script>
    function myCallback(result) {
        if (result.isConfirmed) {
            // Do something.
        }
    }
</script>
```

## 🧩 Extending `ConfirmButton`

You can extend the `ConfirmButton` component to handle logic when the user confirms the SweetAlert dialog. Override the
`callbackAction` method in your subclass to perform custom actions.

---

### 🔧 Example: Custom Live Component

```html

<div class="container">
    <div class="row">
        <div class="col">
            <twig:SweetAlert:ConfirmButton
                    title="Confirm"
                    text="Are you sure?"
                    data-live-item-id-param="1"
            />
        </div>
    </div>
</div>
```

```php
namespace App\LiveComponent;

use Pentiminax\UX\SweetAlert\Twig\Components\ConfirmButton;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

#[AsLiveComponent(template: '@SweetAlert/components/ConfirmButton.html.twig')]
class MyConfirmButton extends ConfirmButton
{
    #[LiveAction]
    public function callbackAction(#[LiveArg] array $result, #[LiveArg] array $args = []): void
    {
        if ($result['isConfirmed'] === true && $args['id'] === '1') {
            // Custom logic goes here
        }
    }
}