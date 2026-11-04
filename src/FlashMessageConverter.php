<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\AlertDefaults;

final class FlashMessageConverter implements FlashMessageConverterInterface
{
    private const KEY_ERROR    = 'error';
    private const KEY_WARNING  = 'warning';
    private const KEY_INFO     = 'info';
    private const KEY_NOTICE   = 'notice';
    private const KEY_QUESTION = 'question';

    public function __construct(
        private readonly AlertDefaults $alertDefaults,
    ) {
    }

    /**
     * @param string[] $messages
     *
     * @return Alert[]
     */
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
            self::KEY_ERROR                  => Icon::ERROR,
            self::KEY_WARNING                => Icon::WARNING,
            self::KEY_INFO, self::KEY_NOTICE => Icon::INFO,
            self::KEY_QUESTION               => Icon::QUESTION,
            default                          => Icon::SUCCESS,
        };
    }
}
