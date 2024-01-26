<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderException;

/**
 * @internal
 */
class RenderContextStack
{
    /** @var RenderContext[] */
    protected array $stack;
    
    public function __construct()
    {
        $this->stack = [];
    }
    
    protected static RenderContextStack $instance;
    
    public static function instance(): RenderContextStack
    {
        return self::$instance ??= new RenderContextStack();
    }
    
    public function pushContext(RenderContext $context): int
    {
        return array_push($this->stack, $context);
    }
    
    public function popContext(): RenderContext|null
    {
        return array_pop($this->stack);
    }
    
    public function getCurrentContext(): RenderContext
    {
        $context = $this->stack[count($this->stack) - 1] ?? null;
        if($context === null) {
            throw new RenderException("No current rendering context.");
        }
        
        return $context;
    }
    
    public function hasContext(): bool
    {
        return count($this->stack) > 0;
    }
}