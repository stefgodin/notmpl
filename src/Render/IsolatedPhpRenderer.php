<?php


namespace Stefmachine\NoTmpl\Render;

/**
 * @internal
 */
class IsolatedPhpRenderer
{
    /**
     * @param string $file
     * @param array $params
     * @return void
     */
    public static function render()
    {
        extract(func_get_arg(1));
        require func_get_arg(0);
    }
}