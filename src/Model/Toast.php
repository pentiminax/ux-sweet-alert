<?php

namespace Pentiminax\UX\SweetAlert\Model;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;

class Toast extends Alert
{
    private Position $position;

    private ?int $timer = null;

    private bool $timerProgressBar = false;

    public static function new(string $id, string $title, string $text, Icon $icon = Icon::SUCCESS): static
    {
        return parent::new($id, $title, $text, $icon);
    }

    public function position(Position $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @param ?int $timer Auto close timer of the popup. Set in ms (milliseconds).
     */
    public function timer(?int $timer): static
    {
        $this->timer = $timer;

        return $this;
    }

    public function withTimerProgressBar(): static
    {
        $this->timerProgressBar = true;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $array =  parent::jsonSerialize();

        $array['position'] = $this->position->value;
        $array['timer'] = $this->timer;
        $array['timerProgressBar'] = $this->timerProgressBar;
        $array['toast'] = true;

        return $array;
    }
}