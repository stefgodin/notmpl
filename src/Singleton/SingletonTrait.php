<?php


namespace StefGodin\NoTmpl\Singleton;

/**
 * @internal
 */
trait SingletonTrait
{
    private static self|null $instance;
    
    public static function instance(): static
    {
        return self::$instance ??= new static();
    }
    
    public static function resetInstance(): void
    {
        self::$instance = null;
    }
}