<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Enum;

/**
 * Animate.css presets for Alert::showClass()/hideClass(). Animate.css must be loaded by the application.
 */
enum Animation: string
{
    case FadeIn      = 'animate__animated animate__fadeIn animate__faster';
    case FadeOut     = 'animate__animated animate__fadeOut animate__faster';
    case FadeInUp    = 'animate__animated animate__fadeInUp animate__faster';
    case FadeOutDown = 'animate__animated animate__fadeOutDown animate__faster';
    case FadeInDown  = 'animate__animated animate__fadeInDown animate__faster';
    case FadeOutUp   = 'animate__animated animate__fadeOutUp animate__faster';
    case ZoomIn      = 'animate__animated animate__zoomIn animate__faster';
    case ZoomOut     = 'animate__animated animate__zoomOut animate__faster';
}
