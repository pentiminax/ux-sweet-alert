<?php

namespace Pentiminax\UX\SweetAlert\Model;

class Toast extends Alert
{
    private ?int $timer = null;

    private bool $timerProgressBar = false;

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

        $array['timer'] = $this->timer;
        $array['timerProgressBar'] = $this->timerProgressBar;
        $array['toast'] = true;

        return $array;
    }
}