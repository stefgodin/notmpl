<?php


namespace StefGodin\NoTmpl\Escape;

use Laminas\Escaper\Escaper as LaminasEscaper;
use StefGodin\NoTmpl\Singleton\SingletonTrait;
use Stringable;

class Esc
{
    use SingletonTrait;
    
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