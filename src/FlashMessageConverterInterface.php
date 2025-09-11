<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Model\Alert;

interface FlashMessageConverterInterface
{
    /**
     * @return Alert[]
     */
    public function convert(string $key, array $messages);
}