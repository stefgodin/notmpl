<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderException;
use Stefmachine\NoTmpl\Singleton\SingletonTrait;

/**
 * @internal
 */
class ComponentStack
{
    use SingletonTrait;
    
    /** @var Component[] */
    private array $stack;
    
    public function __construct()
    {
        $this->stack = [];
    }
    
    public function push(Component $context): static
    {
        $this->stack[] = $context;
        return $this;
    }
    
    public function pop(): static
    {
        array_pop($this->stack);
        return $this;
    }
    
    /**
     * @return Component
     * @throws RenderException
     */
    public function getCurrent(): Component
    {
        if($this->isEmpty()) {
            throw new RenderException("There is no current component.");
        }
        
        return $this->stack[count($this->stack) - 1];
    }
    
    /**
     * @return Component
     * @throws RenderException
     */
    public function getMain(): Component
    {
        if($this->isEmpty()) {
            throw new RenderException("There is no main component.");
        }
        
        return $this->stack[0];
    }
    
    public function isEmpty(): bool
    {
        return empty($this->stack);
    }
    
    public function has(Component $component): bool
    {
        foreach($this->stack as $c) {
            if($c === $component) {
                return true;
            }
        }
        
        return false;
    }
}