<?php


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 */
class IsolatedPhpRenderer
{
    /**
     * @param string $file
     * @param array $params
     * @return void
     * @noinspection PhpDocSignatureInspection
     */
    public static function render(): void
    {
        extract(func_get_arg(1));
        require func_get_arg(0);
    }
}