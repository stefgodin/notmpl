<?php


namespace Stefmachine\NoTmpl\Singleton;

trait SingletonTrait
{
    protected static self|null $instance;
    
    public static function instance(): static
    {
        return self::$instance ??= new static();
    }
    
    public static function resetInstance(): void
    {
        self::$instance = null;
    }
}