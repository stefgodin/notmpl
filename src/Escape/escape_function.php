<?php


namespace Stefmachine\NoTmpl\Escape;

/**
 * Proxies the {@see \Laminas\Escaper\Escaper::escapeHtml()} and stringifies mixed value
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
 * Proxies the {@see \Laminas\Escaper\Escaper::escapeHtmlAttr()} and stringifies mixed value
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
 * Proxies the {@see \Laminas\Escaper\Escaper::escapeJs()} and stringifies mixed value
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
 * Proxies the {@see \Laminas\Escaper\Escaper::escapeCss()} and stringifies mixed value
 *
 * @param mixed $value
 * @return string
 * @throws \Stefmachine\NoTmpl\Exception\EscapeException
 */
function esc_css(mixed $value): string
{
    return Esc::css($value);
}