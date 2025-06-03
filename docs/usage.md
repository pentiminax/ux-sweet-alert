# Usage

## Initialization

To automatically display toasts and alerts in your templates, 
add the following Twig function in your base.html.twig (or the layout file):

```twig
{{ ux_toast_scripts() }}
```

## Toasts

To use UX SweetAlert, inject the `ToastManagerInterface` service and
create toasts in PHP:

```php
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