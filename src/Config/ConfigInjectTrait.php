<?php


namespace Stefmachine\NoTmpl\Config;

/**
 * @internal
 */
trait ConfigInjectTrait
{
    private function getConfig(): Config
    {
        return Config::instance();
    }
}