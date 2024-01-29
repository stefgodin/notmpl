<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderException;
use Stefmachine\NoTmpl\Singleton\SingletonTrait;

/**
 * @internal
 */
class RenderContextStack
{
    use SingletonTrait;
    
    /** @var RenderContext[] */
    private array $stack;
    
    public function __construct()
    {
        $this->stack = [];
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