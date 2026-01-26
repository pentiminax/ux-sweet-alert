<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\AlertDefaults;

class FlashMessageConverter implements FlashMessageConverterInterface
{
    public function __construct(
        private readonly AlertDefaults $alertDefaults,
    ) {
    }

    public function convert(string $key, array $messages): array
    {
        $icon = $this->convertKeyToIcon($key);

        $alerts = [];
        foreach ($messages as $message) {
            $alerts[] = Alert::withDefaults(
                defaults: $this->alertDefaults,
                title: $message,
                icon: $icon,
            );
        }

        return $alerts;
    }

    private function convertKeyToIcon(string $key): Icon
    {
        return match ($key) {
            'error'   => Icon::ERROR,
            'warning' => Icon::WARNING,
            'info', 'notice' => Icon::INFO,
            'question' => Icon::QUESTION,
            default    => Icon::SUCCESS,
        };
    }
}
