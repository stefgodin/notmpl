<?php


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 */
class RawFileHandler
{
    public static function load(string $file): void
    {
        echo file_get_contents($file);
    }
}