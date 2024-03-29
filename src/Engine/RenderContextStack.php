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
            EngineException::throwNoContext("There is no current rendering context started. Are you trying to use NoTMPL functions without using 'render' first?");
        }
        
        return self::$stack[array_key_last(self::$stack)];
    }
}