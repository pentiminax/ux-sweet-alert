<?php

namespace Pentiminax\UX\SweetAlert\Model;

final readonly class Result
{
    public function __construct(
        public bool $isConfirmed = false,
        public bool $isDenied = false,
        public bool $isDismissed = false,
        public mixed $value = null,
    ) {
    }

    public static function fromArray(array $result): self
    {
        return new self(
            isConfirmed: $result['isConfirmed'] ?? false,
            isDenied: $result['isDenied']       ?? false,
            isDismissed: $result['isDismissed'] ?? false,
            value: $result['value']             ?? null
        );
    }
}
