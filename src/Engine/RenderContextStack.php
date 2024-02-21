<?php
/*
 * This file is part of the NoTMPL package.
 *
 * (c) Stéphane Godin
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 */
class RenderContextStack
{
    /** @var RenderContext[] */
    public static array $stack = [];
    
    /**
     * @return RenderContext
     * @throws EngineException
     */
    public static function current(): RenderContext
    {
        if(empty(self::$stack)) {
            throw new EngineException(
                "There is no current rendering context started. Are you trying to use NoTMPL functions without using 'render' first?",
                EngineException::NO_CONTEXT
            );
        }
        
        return self::$stack[count(self::$stack) - 1];
    }
}