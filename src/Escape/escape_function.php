<?php


namespace Stefmachine\NoTmpl\Escape;

/**
 * Proxies {@see Esc::html}
 *
 * @param mixed $value
 * @return string
 * @throws \Stefmachine\NoTmpl\Exception\EscapeException
 */
function esc_html(mixed $value): string
{
    return Esc::html($value);
}

/**
 * Proxies {@see Esc::htmlAttr}
 *
 * @param mixed $value
 * @return string
 * @throws \Stefmachine\NoTmpl\Exception\EscapeException
 */
function esc_html_attr(mixed $value): string
{
    return Esc::htmlAttr($value);
}

/**
 * Proxies {@see Esc::js}
 *
 * @param mixed $value
 * @return string
 * @throws \Stefmachine\NoTmpl\Exception\EscapeException
 */
function esc_js(mixed $value): string
{
    return Esc::js($value);
}

/**
 * Proxies {@see Esc::css}
 *
 * @param mixed $value
 * @return string
 * @throws \Stefmachine\NoTmpl\Exception\EscapeException
 */
function esc_css(mixed $value): string
{
    return Esc::css($value);
}