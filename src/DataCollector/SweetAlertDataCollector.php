<?php

namespace Pentiminax\UX\SweetAlert\DataCollector;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\Cloner\Data;

class SweetAlertDataCollector extends AbstractDataCollector
{
    public function __construct(
        private readonly SweetAlertContextInterface $context
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
        return count($this->data['alerts']);
    }

    public function getNumberOfToasts(): int
    {
        return count($this->data['toasts']);
    }

    public function getTotalCount(): int
    {
        return $this->getNumberOfAlerts() + $this->getNumberOfToasts();
    }

    public function getName(): string
    {
        return 'ux_sweetalert';
    }

    private function collectData(): array
    {
        return [
            'alerts' => $this->context->getAlerts(),
            'toasts' => $this->context->getToasts(),
        ];
    }
}