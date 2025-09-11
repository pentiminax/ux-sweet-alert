<?php

namespace Pentiminax\UX\SweetAlert\Model;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;

class Alert implements \JsonSerializable
{
    private string $id;

    private string $title;

    private string $text;

    private Icon $icon = Icon::SUCCESS;

    private Position $position = Position::CENTER;

    private string $confirmButtonText = 'OK';

    private bool $showConfirmButton = true;

    private bool $showCancelButton = false;

    private bool $showDenyButton = false;

    private bool $animation = true;

    private Theme $theme;

    private bool $backdrop = true;

    private bool $allowOutsideClick = true;

    private bool $allowEscapeKey = true;

    private string $confirmButtonColor = '#3085d6';

    private array $customClass = [];

    private string $cancelButtonText = 'Cancel';

    public static function new(string $title, string $id = '', string $text = '', Icon $icon = Icon::SUCCESS, Position $position = Position::BOTTOM_END, array $customClass = []): static
    {
        $alert = new static();

        $alert->id = empty($id) ? uniqid(more_entropy: true) : $id;
        $alert->title = $title;
        $alert->text = $text;
        $alert->icon = $icon;
        $alert->position = $position;
        $alert->theme = Theme::AUTO;
        $alert->customClass = $customClass;

        return $alert;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getIcon(): Icon
    {
        return $this->icon;
    }

    public function getPosition(): Position
    {
        return $this->position;
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

    public function withDenyButton(): static
    {
        $this->showDenyButton = true;

        return $this;
    }

    public function denyOutsideClick(): static
    {
        $this->allowOutsideClick = true;

        return $this;
    }

    public function denyEscapeKey(): static
    {
        $this->allowEscapeKey = true;

        return $this;
    }

    /**
     * @param string $color Hex color code for the confirm button.
     */
    public function confirmButtonColor(string $color): static
    {
        $this->confirmButtonColor = $color;

        return $this;
    }

    public function position(Position $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function confirmButtonText(string $text): static
    {
        $this->confirmButtonText = $text;

        return $this;
    }

    public function cancelButtonText(string $text): static
    {
        $this->cancelButtonText = $text;

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
            'showDenyButton' => $this->showDenyButton,
            'animation' => $this->animation,
            'theme' => $this->theme->value,
            'backdrop' => $this->backdrop,
            'allowOutsideClick' => $this->allowOutsideClick,
            'allowEscapeKey' => $this->allowEscapeKey,
            'confirmButtonColor' => $this->confirmButtonColor,
            'position' => $this->position->value,
            'customClass' => $this->customClass,
            'cancelButtonText' => $this->cancelButtonText
        ];
    }
}