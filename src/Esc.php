<?php


namespace StefGodin\NoTmpl;

use Stringable;

class Esc
{
    private static function stringify(mixed $value): string
    {
        if(!is_string($value) && !is_scalar($value) && !$value instanceof Stringable) {
            return '';
        }
        
        return $value;
    }
    
    /**
     * Stringifies the mixed value and escapes the value for html and quoted html attributes
     *
     * Can be used for escaping untrusted values put into HTML. The function will also work for quoted HTML attributes
     * but it won't work for illegally quoted or unquoted HTML attributes.
     *
     * If you need better escaping rules or escaping JS and CSS, consider using laminas-escaper
     * {@link https://packagist.org/packages/laminas/laminas-escaper}
     *
     * @param mixed $value
     * @return string
     */
    public static function html(mixed $value): string
    {
        return htmlspecialchars(self::stringify($value), ENT_QUOTES | ENT_SUBSTITUTE);
    }
}