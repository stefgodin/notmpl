<?php


namespace Stefmachine\NoTmpl\Escape;

enum EscapeType
{
    case HTML;
    case HTML_ATTR;
    case JS;
    case CSS;
    case URL;
}