<?php


namespace Stefmachine\NoTmpl\Escape;

/**
 * @param mixed $value
 * @return string
 * @throws \Stefmachine\NoTmpl\Exception\EscapeException
 */
function esc_html(mixed $value): string
{
    return Escaper::html($value);
}

/**
 * @param mixed $value
 * @return string
 * @throws \Stefmachine\NoTmpl\Exception\EscapeException
 */
function esc_html_attr(mixed $value): string
{
    return Escaper::htmlAttr($value);
}

/**
 * @param mixed $value
 * @return string
 * @throws \Stefmachine\NoTmpl\Exception\EscapeException
 */
function esc_js(mixed $value): string
{
    return Escaper::js($value);
}

/**
 * @param mixed $value
 * @return string
 * @throws \Stefmachine\NoTmpl\Exception\EscapeException
 */
function esc_css(mixed $value): string
{
    return Escaper::css($value);
}

/**
 * @param mixed $value
 * @return string
 * @throws \Stefmachine\NoTmpl\Exception\EscapeException
 */
function esc_url(mixed $value): string
{
    return Escaper::url($value);
}