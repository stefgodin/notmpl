<?php


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 */
class PhpFileHandler
{
    public static function render(): void
    {
        extract(func_get_arg(1));
        require func_get_arg(0);
    }
}