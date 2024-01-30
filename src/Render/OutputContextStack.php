<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderException;

class OutputContextStack
{
    /** @var OutputContext[] */
    private array $stack;
    
    public function __construct()
    {
        $this->stack = [];
    }
    
    public function pushContext(OutputContext $context): static
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
     * @return OutputContext
     * @throws RenderException
     */
    public function getCurrentContext(): OutputContext
    {
        if(!$this->hasContext()) {
            throw new RenderException("There is no current output context.");
        }
        
        return $this->stack[count($this->stack) - 1];
    }
    
    /**
     * @return OutputContext
     * @throws RenderException
     */
    public function getMainContext(): OutputContext
    {
        if(!$this->hasContext()) {
            throw new RenderException("There is no main output context.");
        }
        
        return $this->stack[0];
    }
    
    public function hasContext(): bool
    {
        return !empty($this->stack);
    }
}