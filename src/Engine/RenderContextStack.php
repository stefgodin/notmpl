<?php


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 */
class RenderContextStack
{
    /** @var RenderContext[] */
    public static array $stack = [];
    
    public static function current(): RenderContext
    {
        if(empty(self::$stack)) {
            throw new EngineException(
                "There is no current rendering context started. Are you trying to use NoTMPL functions without using 'render' first?",
                EngineException::CTX_NO_CONTEXT
            );
        }
        
        return self::$stack[count(self::$stack) - 1];
    }
}