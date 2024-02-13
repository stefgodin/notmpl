<?php


namespace StefGodin\NoTmpl;

/**
 * Proxies {@see Esc::html}
 *
 * @param mixed $value
 * @return string
 */
function esc_html(mixed $value): string
{
    return Esc::html($value);
}