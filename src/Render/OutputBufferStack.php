<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderException;

/**
 * @internal
 */
class OutputBufferStack
{
    /** @var OutputBuffer[] */
    private array $stack;
    
    public function __construct()
    {
        $this->stack = [];
    }
    
    public function push(OutputBuffer $ob): static
    {
        $this->stack[] = $ob;
        return $this;
    }
    
    public function pop(): static
    {
        array_pop($this->stack);
        return $this;
    }
    
    /**
     * @return OutputBuffer
     * @throws RenderException
     */
    public function getCurrent(): OutputBuffer
    {
        if($this->isEmpty()) {
            throw new RenderException("There is no current output buffer.");
        }
        
        return $this->stack[count($this->stack) - 1];
    }
    
    public function isEmpty(): bool
    {
        return empty($this->stack);
    }
}