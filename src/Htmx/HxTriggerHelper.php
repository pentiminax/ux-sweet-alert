<?php

namespace Pentiminax\UX\SweetAlert\Htmx;

use Pentiminax\UX\SweetAlert\Model\Alert;
use Symfony\Component\HttpFoundation\Response;

class HxTriggerHelper
{
    public const EVENT_KEY = 'ux-sweet-alert:alert:added';

    public static function withAlert(Response $response, Alert $alert): Response
    {
        return self::withTrigger($response, [
            self::EVENT_KEY => [
                'alert' => $alert->jsonSerialize(),
            ],
        ]);
    }

    private static function withTrigger(Response $response, array $trigger): Response
    {
        $response->headers->set('HX-Trigger', json_encode($trigger));

        return $response;
    }
}
