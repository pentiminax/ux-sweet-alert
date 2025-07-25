<?php

namespace Pentiminax\UX\SweetAlert\Inspector;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector as BaseDataCollector;
use Symfony\Component\VarDumper\Cloner\Data;

class DataCollector extends BaseDataCollector
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