<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\Model\Alert;

class FlashMessageConverter implements FlashMessageConverterInterface
{
    private Theme $defaultTheme;

    public function __construct(string $defaultTheme = Theme::Auto->value)
    {
        $this->defaultTheme = Theme::from($defaultTheme);
    }

    public function convert(string $key, array $messages): array
    {
        $icon = $this->convertKeyToIcon($key);

        $alerts = [];
        foreach ($messages as $message) {
            $alerts[] = Alert::new(
                title: $message,
                icon: $icon,
                position: Position::CENTER
            )->theme($this->defaultTheme);
        }

        return $alerts;
    }

    private function convertKeyToIcon(string $key): Icon
    {
        return match($key) {
            'error' => Icon::ERROR,
            'warning' => Icon::WARNING,
            'info', 'notice' => Icon::INFO,
            'question' => Icon::QUESTION,
            default => Icon::SUCCESS,
        };
    }
}
