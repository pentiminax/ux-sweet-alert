<?php

namespace Pentiminax\UX\SweetAlert\Model;

use JsonSerializable;
use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Theme;

class Alert implements JsonSerializable
{
    private string $id;
    private string $title;
    private string $text;
    private Icon $icon = Icon::SUCCESS;
    private string $confirmButtonText = 'OK';
    private bool $showConfirmButton = true;
    private bool $showCancelButton = false;
    private bool $animation = true;
    private Theme $theme;
    private bool $backdrop = true;

    public static function new(string $id, string $title, string $text, Icon $icon = Icon::SUCCESS): static
    {
        $alert = new static();

        $alert->id = $id;
        $alert->title = $title;
        $alert->text = $text;
        $alert->icon = $icon;
        $alert->theme = Theme::AUTO;

        return $alert;
    }

    public function withoutAnimation(): static
    {
        $this->animation = false;

        return $this;
    }

    public function withoutBackdrop(): static
    {
        $this->backdrop = false;

        return $this;
    }

    public function withCancelButton(): static
    {
        $this->showCancelButton = true;

        return $this;
    }

    public function theme(Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function withoutConfirmButton(): static
    {
        $this->showConfirmButton = false;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'text' => $this->text,
            'icon' => $this->icon->value,
            'confirmButtonText' => $this->confirmButtonText,
            'showConfirmButton' => $this->showConfirmButton,
            'showCancelButton' => $this->showCancelButton,
            'animation' => $this->animation,
            'theme' => $this->theme->value,
            'backdrop' => $this->backdrop,
        ];
    }
}