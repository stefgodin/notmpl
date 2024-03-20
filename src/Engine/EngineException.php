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

use Exception;

/**
 * Any exception and error from the NoTMPL engine
 */
class EngineException extends Exception
{
    const NO_CONTEXT = 101;
    const ILLEGAL_BUFFER_ACTION = 201;
    const INVALID_TREE_STRUCTURE = 301;
    const FILE_NOT_FOUND = 401;
    const NO_FILE_HANDLER = 402;
    
    public static function throwNoContext(string $message): never
    {
        throw new EngineException($message, self::NO_CONTEXT);
    }
    
    public static function throwIllegalOb(string $message): never
    {
        throw new EngineException($message, self::ILLEGAL_BUFFER_ACTION);
    }
    
    public static function throwInvalidTreeStructure(string $message): never
    {
        throw new EngineException($message, self::INVALID_TREE_STRUCTURE);
    }
}