<?php

namespace Pentiminax\UX\SweetAlert;

interface FlashMessageConverterInterface
{
    public function convert(string $key, array $messages);
}