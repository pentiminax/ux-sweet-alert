<?php

namespace Pentiminax\UX\SweetAlert\Model;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;

final class Alert implements \JsonSerializable
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

    private bool $reverseButtons = false;

    private bool $animation = true;

    private Theme $theme;

    private bool $backdrop = true;

    private bool $allowOutsideClick = true;

    private bool $allowEscapeKey = true;

    private string $confirmButtonColor = '#3085d6';

    private string $cancelButtonColor = '#aaa';

    private string $denyButtonColor = '#dd6b55';

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

    private ?string $inputLabel = null;

    private ?string $inputPlaceholder = null;

    private ?string $inputValue = null;

    private array $inputAttributes = [];

    public static function new(string $title, string $id = '', string $text = '', ?Icon $icon = Icon::SUCCESS, Position $position = Position::BOTTOM_END, array $customClass = []): self
    {
        $alert = new self();

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
    ): self {
        $alert = new self();

        $alert->id                 = empty($id) ? uniqid(more_entropy: true) : $id;
        $alert->title              = $title;
        $alert->text               = $text;
        $alert->icon               = $icon;
        $alert->position           = $position        ?? $defaults->position;
        $alert->theme              = $defaults->theme ?? Theme::Auto;
        $alert->customClass        = $customClass     ?? $defaults->customClass;
        $alert->confirmButtonColor = $defaults->confirmButtonColor;
        $alert->cancelButtonColor  = $defaults->cancelButtonColor;
        $alert->denyButtonColor    = $defaults->denyButtonColor;
        $alert->confirmButtonText  = $defaults->confirmButtonText;
        $alert->cancelButtonText   = $defaults->cancelButtonText;
        $alert->denyButtonText     = $defaults->denyButtonText;
        $alert->showConfirmButton  = $defaults->showConfirmButton;
        $alert->showCancelButton   = $defaults->showCancelButton;
        $alert->showDenyButton     = $defaults->showDenyButton;
        $alert->reverseButtons     = $defaults->reverseButtons;
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

    public function withoutAnimation(): self
    {
        $this->animation = false;

        return $this;
    }

    public function withoutBackdrop(): self
    {
        $this->backdrop = false;

        return $this;
    }

    public function withCancelButton(): self
    {
        $this->showCancelButton = true;

        return $this;
    }

    public function theme(Theme $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function withoutConfirmButton(): self
    {
        $this->showConfirmButton = false;

        return $this;
    }

    public function withDenyButton(): self
    {
        $this->showDenyButton = true;

        return $this;
    }

    public function denyOutsideClick(): self
    {
        $this->allowOutsideClick = false;

        return $this;
    }

    public function denyEscapeKey(): self
    {
        $this->allowEscapeKey = false;

        return $this;
    }

    /**
     * @param string $color hex color code for the confirm button
     */
    public function confirmButtonColor(string $color): self
    {
        $this->confirmButtonColor = $color;

        return $this;
    }

    /**
     * @param string $color hex color code for the cancel button
     */
    public function cancelButtonColor(string $color): self
    {
        $this->cancelButtonColor = $color;

        return $this;
    }

    /**
     * @param string $color hex color code for the deny button
     */
    public function denyButtonColor(string $color): self
    {
        $this->denyButtonColor = $color;

        return $this;
    }

    public function reverseButtons(bool $reverseButtons = true): self
    {
        $this->reverseButtons = $reverseButtons;

        return $this;
    }

    public function position(Position $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function confirmButtonText(string $text): self
    {
        $this->confirmButtonText = $text;

        return $this;
    }

    public function cancelButtonText(string $text): self
    {
        $this->cancelButtonText = $text;

        return $this;
    }

    public function html(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function asToast(): self
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
    public function timer(?int $timer): self
    {
        $this->timer = $timer;

        return $this;
    }

    public function withTimerProgressBar(): self
    {
        $this->timerProgressBar = true;

        return $this;
    }

    public function getFooter(): ?string
    {
        return $this->footer;
    }

    public function setFooter(?string $footer): self
    {
        $this->footer = $footer;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getImageAlt(): ?string
    {
        return $this->imageAlt;
    }

    public function setImageAlt(?string $imageAlt): self
    {
        $this->imageAlt = $imageAlt;

        return $this;
    }

    public function getImageHeight(): ?int
    {
        return $this->imageHeight;
    }

    public function setImageHeight(?int $imageHeight): self
    {
        $this->imageHeight = $imageHeight;

        return $this;
    }

    public function setDraggable(bool $draggable = true): self
    {
        $this->draggable = $draggable;

        return $this;
    }

    public function isDraggable(): bool
    {
        return $this->draggable;
    }

    public function setFocusConfirm(bool $focusConfirm = true): self
    {
        $this->focusConfirm = $focusConfirm;

        return $this;
    }

    public function isFocusConfirm(): bool
    {
        return $this->focusConfirm;
    }

    public function setDenyButtonText(string $denyButtonText): self
    {
        $this->denyButtonText = $denyButtonText;

        return $this;
    }

    public function getDenyButtonText(): string
    {
        return $this->denyButtonText;
    }

    public function setTopLayer(bool $topLayer = true): self
    {
        $this->topLayer = $topLayer;

        return $this;
    }

    public function input(?string $input): self
    {
        $this->input = $input;

        return $this;
    }

    public function getInput(): ?string
    {
        return $this->input;
    }

    public function inputPlaceholder(?string $inputPlaceholder): self
    {
        $this->inputPlaceholder = $inputPlaceholder;

        return $this;
    }

    public function getInputPlaceholder(): ?string
    {
        return $this->inputPlaceholder;
    }

    public function inputValue(?string $inputValue): self
    {
        $this->inputValue = $inputValue;

        return $this;
    }

    public function getInputValue(): ?string
    {
        return $this->inputValue;
    }

    public function inputLabel(?string $inputLabel): self
    {
        $this->inputLabel = $inputLabel;

        return $this;
    }

    public function getInputLabel(): ?string
    {
        return $this->inputLabel;
    }

    public function inputAttributes(array $inputAttributes): self
    {
        $this->inputAttributes = $inputAttributes;

        return $this;
    }

    public function getInputAttributes(): array
    {
        return $this->inputAttributes;
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
            'reverseButtons'     => $this->reverseButtons,
            'animation'          => $this->animation,
            'theme'              => $this->theme->value,
            'allowEscapeKey'     => $this->allowEscapeKey,
            'confirmButtonColor' => $this->confirmButtonColor,
            'cancelButtonColor'  => $this->cancelButtonColor,
            'denyButtonColor'    => $this->denyButtonColor,
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
            'inputLabel'         => $this->inputLabel,
            'inputAttributes'    => $this->inputAttributes,
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
