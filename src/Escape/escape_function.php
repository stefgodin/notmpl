<?php


namespace Stefmachine\NoTmpl\Escape;

function esc(mixed $value, EscapeType $type = EscapeType::HTML): string
{
    return match ($type) {
        EscapeType::HTML => esc_html($value),
        EscapeType::HTML_ATTR => esc_html_attr($value),
        EscapeType::JS => esc_js($value),
        EscapeType::CSS => esc_css($value),
        EscapeType::URL => esc_url($value),
    };
}

function esc_html(mixed $value): string
{
    return Escaper::html($value);
}

function esc_html_attr(mixed $value): string
{
    return Escaper::htmlAttr($value);
}

function esc_js(mixed $value): string
{
    return Escaper::js($value);
}

function esc_css(mixed $value): string
{
    return Escaper::css($value);
}

function esc_url(mixed $value): string
{
    return Escaper::url($value);
}