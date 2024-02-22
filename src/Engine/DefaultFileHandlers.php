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

class DefaultFileHandlers
{
    const PHP = '/^.+\.php$/';
    const HTML = '/^.*\.html$/';
    
    public static function php(): void
    {
        extract(func_get_arg(1));
        require func_get_arg(0);
    }
    
    public static function raw(): void
    {
        echo file_get_contents(func_get_arg(0));
    }
    
    public static function merge(array $handlers): array
    {
        $handlers[self::PHP] ??= self::php(...);
        $handlers[self::HTML] ??= self::raw(...);
        
        return $handlers;
    }
}