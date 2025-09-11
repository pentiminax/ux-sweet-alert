<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Model\Alert;

class FlashMessageConverter
{
    public function convert(string $key, array $messages): array
    {
        $icon = $this->convertKeyToIcon($key);

        $alerts = [];
        foreach ($messages as $message) {
            $alerts[] = Alert::new(
                title: $message,
                icon: $icon,
                position: Position::CENTER
            );
        }

        return $alerts;
    }

    private function convertKeyToIcon(string $key): Icon
    {
        return match($key) {
            'error' => Icon::ERROR,
            'warning' => Icon::WARNING,
            'info' => Icon::INFO,
            default => Icon::SUCCESS,
        };
    }
}