<?php

namespace Pentiminax\UX\SweetAlert\Enum;

enum Theme: string
{
    case Light           = 'light';
    case Dark            = 'dark';
    case Auto            = 'auto';
    case Borderless      = 'borderless';
    case Bootstrap4      = 'bootstrap-4';
    case Bootstrap4Light = 'bootstrap-4-light';
    case Bootstrap4Dark  = 'bootstrap-4-dark';
    case Bootstrap5      = 'bootstrap-5';
    case Bootstrap5Light = 'bootstrap-5-light';
    case Bootstrap5Dark  = 'bootstrap-5-dark';
    case MaterialUI      = 'material-ui';
    case MaterialUILight = 'material-ui-light';
    case MaterialUIDark  = 'material-ui-dark';
}
