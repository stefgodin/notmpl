<?php


namespace Stefmachine\NoTmpl\Config;

trait ConfigInjectTrait
{
    protected function getConfig(): Config
    {
        return Config::instance();
    }
}