<?php


namespace Stefmachine\NoTmpl\Escape;

use Laminas\Escaper\Escaper as LaminasEscaper;
use Laminas\Escaper\Exception\ExceptionInterface as LaminasExceptionInterface;
use Stefmachine\NoTmpl\Config\ConfigInjectTrait;
use Stefmachine\NoTmpl\Exception\EscapeException;
use Stefmachine\NoTmpl\Singleton\SingletonTrait;
use Stringable;

class Esc
{
    use SingletonTrait;
    use ConfigInjectTrait;
    
    private LaminasEscaper $escaper;
    
    private function __construct() {}
    
    /**
     * @return LaminasEscaper
     * @throws LaminasExceptionInterface
     */
    private function getEscaper(): LaminasEscaper
    {
        return $this->escaper ??= new LaminasEscaper($this->getConfig()->getEscaperEncoding());
    }
    
    private static function stringify(mixed $value): string
    {
        if(!is_string($value) && !is_scalar($value) && !$value instanceof Stringable) {
            return '';
        }
        
        return $value;
    }
    
    /**
     * @param mixed $value
     * @return string
     * @throws EscapeException
     */
    public static function html(mixed $value): string
    {
        try {
            return self::instance()->getEscaper()->escapeHtml(self::stringify($value));
        } catch(LaminasExceptionInterface $ex) {
            throw new EscapeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
    
    /**
     * @param mixed $value
     * @return string
     * @throws EscapeException
     */
    public static function htmlAttr(mixed $value): string
    {
        try {
            return self::instance()->getEscaper()->escapeHtmlAttr(self::stringify($value));
        } catch(LaminasExceptionInterface $ex) {
            throw new EscapeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
    
    /**
     * @param mixed $value
     * @return string
     * @throws EscapeException
     */
    public static function js(mixed $value): string
    {
        try {
            return self::instance()->getEscaper()->escapeJs(self::stringify($value));
        } catch(LaminasExceptionInterface $ex) {
            throw new EscapeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
    
    /**
     * @param mixed $value
     * @return string
     * @throws EscapeException
     */
    public static function css(mixed $value): string
    {
        try {
            return self::instance()->getEscaper()->escapeCss(self::stringify($value));
        } catch(LaminasExceptionInterface $ex) {
            throw new EscapeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}