<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\DataCollector;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\Cloner\Data;

final class SweetAlertDataCollector extends AbstractDataCollector
{
    private const NAME       = 'ux_sweetalert';
    private const KEY_ALERTS = 'alerts';
    private const KEY_TOASTS = 'toasts';

    public function __construct(
        private readonly SweetAlertContextInterface $context,
    ) {
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $collectedData = [];
        foreach ($this->collectData() as $key => $value) {
            $collectedData[$key] = $this->cloneVar($value);
        }

        $this->data = $collectedData;
    }

    public function getData(): array|Data
    {
        return $this->data;
    }

    public function getNumberOfAlerts(): int
    {
        return \count($this->data[self::KEY_ALERTS]);
    }

    public function getNumberOfToasts(): int
    {
        return \count($this->data[self::KEY_TOASTS]);
    }

    public function getTotalCount(): int
    {
        return $this->getNumberOfAlerts() + $this->getNumberOfToasts();
    }

    public function getName(): string
    {
        return self::NAME;
    }

    private function collectData(): array
    {
        return [
            self::KEY_ALERTS => $this->context->getStandardAlerts(),
            self::KEY_TOASTS => $this->context->getToasts(),
        ];
    }
}
