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
    
    public function pushContext(RenderContext $context): static
    {
        $this->stack[] = $context;
        return $this;
    }
    
    public function popContext(): static
    {
        array_pop($this->stack);
        return $this;
    }
    
    /**
     * @return RenderContext
     * @throws RenderException
     */
    public function getCurrentContext(): RenderContext
    {
        if(!$this->hasContext()) {
            throw new RenderException("There is no current rendering context.");
        }
        
        return $this->stack[count($this->stack) - 1];
    }
    
    public function hasContext(): bool
    {
        return !empty($this->stack);
    }
}