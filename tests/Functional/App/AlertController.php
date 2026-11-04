<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Functional\App;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Model\Result;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Twig\Environment;

final class AlertController
{
    /** @var array<string, string> path => action */
    public const ROUTES = [
        '/alert-then-redirect' => 'alertThenRedirect',
        '/page'                => 'page',
        '/flash-then-redirect' => 'flashThenRedirect',
        '/json'                => 'json',
        '/htmx'                => 'htmx',
        '/turbo-stream'        => 'turboStream',
        '/turbo-frame'         => 'turboFrame',
        '/result'              => 'result',
    ];

    public const TITLE = 'Profile saved';

    public function __construct(
        private readonly AlertManagerInterface $alertManager,
        private readonly Environment $twig,
    ) {
    }

    public function alertThenRedirect(): Response
    {
        $this->alertManager->success(title: self::TITLE, text: 'Your changes were saved.');

        return new RedirectResponse('/page');
    }

    public function flashThenRedirect(Request $request): Response
    {
        $session = $request->getSession();
        \assert($session instanceof FlashBagAwareSessionInterface);
        $session->getFlashBag()->add('error', 'Payment refused');

        return new RedirectResponse('/page');
    }

    public function page(): Response
    {
        return new Response($this->twig->createTemplate('<body>{{ ux_sweet_alert_scripts() }}</body>')->render());
    }

    public function json(): Response
    {
        $this->alertManager->warning(title: self::TITLE, position: Position::TOP_END);

        return new JsonResponse(['saved' => true]);
    }

    public function htmx(): Response
    {
        $this->alertManager->toast(title: self::TITLE);

        return new Response('<div>saved</div>', headers: ['HX-Trigger' => 'profileSaved']);
    }

    public function turboStream(): Response
    {
        $this->alertManager->info(title: self::TITLE);

        return new Response('<turbo-stream action="remove" target="form"></turbo-stream>', headers: [
            'Content-Type' => 'text/vnd.turbo-stream.html',
        ]);
    }

    // No Content-Type on purpose: like AbstractController::render(), Symfony only sets it when preparing the response.
    public function turboFrame(): Response
    {
        $this->alertManager->info(title: self::TITLE);

        return new Response('<turbo-frame id="profile">saved</turbo-frame>');
    }

    public function result(Result $result): Response
    {
        return new JsonResponse([
            'isConfirmed' => $result->isConfirmed,
            'isDenied'    => $result->isDenied,
            'isDismissed' => $result->isDismissed,
            'value'       => $result->value,
        ]);
    }
}
