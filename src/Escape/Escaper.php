<?php


namespace Stefmachine\NoTmpl\Escape;

use Laminas\Escaper\Escaper as LaminasEscaper;
use Laminas\Escaper\Exception\ExceptionInterface;
use Stefmachine\NoTmpl\Exception\EscapeException;
use Stringable;

class Escaper
{
    protected static string|null $encoding = null;
    protected static LaminasEscaper|null $instance = null;
    
    /**
     * @return LaminasEscaper
     * @throws ExceptionInterface
     */
    private static function instance(): LaminasEscaper
    {
        return self::$instance ??= new LaminasEscaper(self::$encoding);
    }
    
    public static function setEncoding(string|null $_encoding): void
    {
        try {
            self::$encoding = $_encoding;
            if(self::instance()->getEncoding() !== $_encoding) {
                self::$instance = null;
            }
        } catch(ExceptionInterface $ex) {
            throw new EscapeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
    
    private function __construct() {}
    
    private static function stringify(mixed $value): string
    {
        if(!is_string($value) && !is_scalar($value) && !$value instanceof Stringable) {
            return '';
        }
        
        return $value;
    }
    
    public static function html(mixed $value): string
    {
        try {
            return self::instance()->escapeHtml(self::stringify($value));
        } catch(ExceptionInterface $ex) {
            throw new EscapeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
    
    public static function htmlAttr(mixed $value): string
    {
        try {
            return self::instance()->escapeHtmlAttr(self::stringify($value));
        } catch(ExceptionInterface $ex) {
            throw new EscapeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
    
    public static function js(mixed $value): string
    {
        try {
            return self::instance()->escapeJs(self::stringify($value));
        } catch(ExceptionInterface $ex) {
            throw new EscapeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
    
    public static function css(mixed $value): string
    {
        try {
            return self::instance()->escapeCss(self::stringify($value));
        } catch(ExceptionInterface $ex) {
            throw new EscapeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
    
    public static function url(mixed $value): string
    {
        try {
            return self::instance()->escapeUrl(self::stringify($value));
        } catch(ExceptionInterface $ex) {
            throw new EscapeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}