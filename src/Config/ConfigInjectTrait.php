<?php


namespace Stefmachine\NoTmpl\Config;

trait ConfigInjectTrait
{
    private function getConfig(): Config
    {
        return Config::instance();
    }
}