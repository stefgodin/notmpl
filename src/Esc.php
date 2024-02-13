<?php


namespace StefGodin\NoTmpl;

use Laminas\Escaper\Escaper as LaminasEscaper;
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
     * Stringifies the mixed value and proxies the {@see LaminasEscaper::escapeHtml}
     *
     * @param mixed $value
     * @return string
     */
    public static function html(mixed $value): string
    {
        return htmlspecialchars(self::stringify($value), ENT_QUOTES | ENT_SUBSTITUTE);
    }
}