<?php

declare(strict_types=1);

namespace App;

enum Page: string
{
    private const DOCS_URL = 'https://pentiminax.github.io/ux-sweet-alert/';

    case Alerts         = 'alerts';
    case Toasts         = 'toasts';
    case InputDialogs   = 'input-dialogs';
    case Themes         = 'themes';
    case Configuration  = 'configuration';
    case Turbo          = 'turbo';
    case LiveComponents = 'live-components';

    public function title(): string
    {
        return match ($this) {
            self::Alerts         => 'Alerts',
            self::Toasts         => 'Toasts',
            self::InputDialogs   => 'Input dialogs',
            self::Themes         => 'Themes',
            self::Configuration  => 'Configuration',
            self::Turbo          => 'Turbo',
            self::LiveComponents => 'Live Components',
        };
    }

    public function group(): string
    {
        return match ($this) {
            self::Turbo, self::LiveComponents => 'Integrations',
            default                           => 'Guide',
        };
    }

    public function docsUrl(): string
    {
        return self::DOCS_URL.match ($this) {
            self::Alerts         => 'guide/alerts/',
            self::Toasts         => 'guide/toasts/',
            self::InputDialogs   => 'guide/input-dialogs/',
            self::Themes         => 'guide/themes-and-styling/',
            self::Configuration  => 'guide/configuration/',
            self::Turbo          => 'integrations/turbo/',
            self::LiveComponents => 'integrations/live-components/',
        };
    }

    /**
     * @return array<string, self[]>
     */
    public static function grouped(): array
    {
        $groups = [];
        foreach (self::cases() as $page) {
            $groups[$page->group()][] = $page;
        }

        return $groups;
    }
}
