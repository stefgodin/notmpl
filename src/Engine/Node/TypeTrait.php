<?php
/*
 * This file is part of the NoTMPL package.
 *
 * (c) Stéphane Godin
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */


namespace StefGodin\NoTmpl\Engine\Node;

use ReflectionClass;

trait TypeTrait
{
    private static string $type;
    
    public static function getType(): string
    {
        if(!isset($type)) {
            $shortName = (new ReflectionClass(static::class))->getShortName();
            $shortName = str_replace('Node', '', $shortName) ?: $shortName;
            self::$type = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', $shortName)), '-');
        }
        
        return self::$type;
    }
}