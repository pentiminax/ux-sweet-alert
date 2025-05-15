<?php

namespace Pentiminax\UX\SweetAlert\Model;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;

class Toast extends Alert
{
    private Position $position;

    public static function new(string $id, string $title, string $text, Icon $icon = Icon::SUCCESS): static
    {
        return parent::new($id, $title, $text, $icon);
    }

    public function position(Position $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $array =  parent::jsonSerialize();

        $array['position'] = $this->position->value;
        $array['toast'] = true;

        return $array;
    }
}