<?php


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 */
class RenderContextStack
{
    /** @var \StefGodin\NoTmpl\Engine\RenderContext[] */
    public static array $stack = [];
    
    public static function current(): \StefGodin\NoTmpl\Engine\RenderContext
    {
        if(empty(self::$stack)) {
            throw new \StefGodin\NoTmpl\Engine\EngineException(
                "There is no current rendering context started. Are you trying to use NoTMPL functions without using 'render' first?",
                \StefGodin\NoTmpl\Engine\EngineException::NO_CONTEXT
            );
        }
        
        return self::$stack[count(self::$stack) - 1];
    }
}