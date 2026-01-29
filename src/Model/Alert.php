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

    private ?Icon $icon = Icon::SUCCESS;

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

    private ?string $html = null;

    private bool $toast = false;

    private ?int $timer = null;

    private bool $timerProgressBar = false;

    private ?string $footer = null;

    private ?string $imageUrl = null;

    private ?int $imageHeight = null;

    private ?string $imageAlt = null;

    private bool $draggable = false;

    private bool $focusConfirm = true;

    private string $denyButtonText = 'No';

    private bool $topLayer = false;

    private ?string $input = null;

    private ?string $inputPlaceholder = null;

    private ?string $inputValue = null;

    public static function new(string $title, string $id = '', string $text = '', ?Icon $icon = Icon::SUCCESS, Position $position = Position::BOTTOM_END, array $customClass = []): static
    {
        $alert = new static();

        $alert->id          = empty($id) ? uniqid(more_entropy: true) : $id;
        $alert->title       = $title;
        $alert->text        = $text;
        $alert->icon        = $icon;
        $alert->position    = $position;
        $alert->theme       = Theme::Auto;
        $alert->customClass = $customClass;

        return $alert;
    }

    public static function withDefaults(
        AlertDefaults $defaults,
        string $title,
        string $id = '',
        string $text = '',
        ?Icon $icon = Icon::SUCCESS,
        ?Position $position = null,
        ?array $customClass = null,
    ): static {
        $alert = new static();

        $alert->id                 = empty($id) ? uniqid(more_entropy: true) : $id;
        $alert->title              = $title;
        $alert->text               = $text;
        $alert->icon               = $icon;
        $alert->position           = $position        ?? $defaults->position;
        $alert->theme              = $defaults->theme ?? Theme::Auto;
        $alert->customClass        = $customClass     ?? $defaults->customClass;
        $alert->confirmButtonColor = $defaults->confirmButtonColor;
        $alert->confirmButtonText  = $defaults->confirmButtonText;
        $alert->cancelButtonText   = $defaults->cancelButtonText;
        $alert->denyButtonText     = $defaults->denyButtonText;
        $alert->showConfirmButton  = $defaults->showConfirmButton;
        $alert->showCancelButton   = $defaults->showCancelButton;
        $alert->showDenyButton     = $defaults->showDenyButton;
        $alert->backdrop           = $defaults->backdrop;
        $alert->animation          = $defaults->animation;
        $alert->allowOutsideClick  = $defaults->allowOutsideClick;
        $alert->allowEscapeKey     = $defaults->allowEscapeKey;
        $alert->focusConfirm       = $defaults->focusConfirm;
        $alert->draggable          = $defaults->draggable;
        $alert->topLayer           = $defaults->topLayer;
        $alert->timer              = $defaults->timer;
        $alert->timerProgressBar   = $defaults->timerProgressBar;

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

    public function getIcon(): ?Icon
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
        $this->allowOutsideClick = false;

        return $this;
    }

    public function denyEscapeKey(): static
    {
        $this->allowEscapeKey = false;

        return $this;
    }

    /**
     * @param string $color hex color code for the confirm button
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

    public function html(string $html): static
    {
        $this->html = $html;

        return $this;
    }

    public function asToast(): static
    {
        $this->toast = true;

        return $this;
    }

    public function isToast(): bool
    {
        return $this->toast;
    }

    /**
     * @param int|null $timer Auto close timer of the popup. Set in ms (milliseconds).
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

    public function getFooter(): ?string
    {
        return $this->footer;
    }

    public function setFooter(?string $footer): static
    {
        $this->footer = $footer;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getImageAlt(): ?string
    {
        return $this->imageAlt;
    }

    public function setImageAlt(?string $imageAlt): static
    {
        $this->imageAlt = $imageAlt;

        return $this;
    }

    public function getImageHeight(): ?int
    {
        return $this->imageHeight;
    }

    public function setImageHeight(?int $imageHeight): static
    {
        $this->imageHeight = $imageHeight;

        return $this;
    }

    public function setDraggable(bool $draggable = true): static
    {
        $this->draggable = $draggable;

        return $this;
    }

    public function isDraggable(): bool
    {
        return $this->draggable;
    }

    public function setFocusConfirm(bool $focusConfirm = true): static
    {
        $this->focusConfirm = $focusConfirm;

        return $this;
    }

    public function isFocusConfirm(): bool
    {
        return $this->focusConfirm;
    }

    public function setDenyButtonText(string $denyButtonText): static
    {
        $this->denyButtonText = $denyButtonText;

        return $this;
    }

    public function getDenyButtonText(): string
    {
        return $this->denyButtonText;
    }

    public function setTopLayer(bool $topLayer = true): static
    {
        $this->topLayer = $topLayer;

        return $this;
    }

    public function input(?string $input): static
    {
        $this->input = $input;

        return $this;
    }

    public function getInput(): ?string
    {
        return $this->input;
    }

    public function inputPlaceholder(?string $inputPlaceholder): static
    {
        $this->inputPlaceholder = $inputPlaceholder;

        return $this;
    }

    public function getInputPlaceholder(): ?string
    {
        return $this->inputPlaceholder;
    }

    public function inputValue(?string $inputValue): static
    {
        $this->inputValue = $inputValue;

        return $this;
    }

    public function getInputValue(): ?string
    {
        return $this->inputValue;
    }

    public function jsonSerialize(): array
    {
        $data = [
            'id'                 => $this->id,
            'title'              => $this->title,
            'text'               => $this->text,
            'icon'               => $this->icon?->value,
            'confirmButtonText'  => $this->confirmButtonText,
            'showConfirmButton'  => $this->showConfirmButton,
            'showCancelButton'   => $this->showCancelButton,
            'showDenyButton'     => $this->showDenyButton,
            'animation'          => $this->animation,
            'theme'              => $this->theme->value,
            'allowEscapeKey'     => $this->allowEscapeKey,
            'confirmButtonColor' => $this->confirmButtonColor,
            'position'           => $this->position->value,
            'customClass'        => $this->customClass,
            'cancelButtonText'   => $this->cancelButtonText,
            'html'               => $this->html,
            'footer'             => $this->footer,
            'imageUrl'           => $this->imageUrl,
            'imageHeight'        => $this->imageHeight,
            'imageAlt'           => $this->imageAlt,
            'draggable'          => $this->draggable,
            'focusConfirm'       => $this->focusConfirm,
            'topLayer'           => $this->topLayer,
            'input'              => $this->input,
            'inputPlaceholder'   => $this->inputPlaceholder,
            'inputValue'         => $this->inputValue,
        ];

        if ($this->toast) {
            $data['toast']            = true;
            $data['timer']            = $this->timer;
            $data['timerProgressBar'] = $this->timerProgressBar;
        } else {
            $data['backdrop']          = $this->backdrop;
            $data['allowOutsideClick'] = $this->allowOutsideClick;
        }

        return $data;
    }
}
