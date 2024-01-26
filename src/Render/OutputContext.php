<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderException;

/**
 * @internal
 */
class OutputContext
{
    protected string|null $output;
    protected int|null $level;
    
    public function __construct(
        protected string $name,
    )
    {
        $this->output = null;
        $this->level = null;
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function open(): static
    {
        if($this->isOpen()) {
            throw new RenderException("The output context '{$this->name}' is already opened.");
        }
        
        ob_start(function(string $buffer) {
            $this->output = $buffer;
            return "";
        });
        $this->level = ob_get_level();
        return $this;
    }
    
    public function wasOpened(): bool
    {
        return $this->level !== null;
    }
    
    public function isOpen(): bool
    {
        return $this->wasOpened() && !$this->isClosed();
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function close(): static
    {
        if($this->isClosed()) {
            throw new RenderException("The output context '{$this->name}' is already closed.");
        }
        
        if(!$this->wasOpened()) {
            throw new RenderException("The output context '{$this->name}' cannot be closed because it was never opened in the first place.");
        }
        
        if($this->level !== ob_get_level()) {
            throw new RenderException("The output context '{$this->name}' precedes other non-closed output contexts/buffers.");
        }
        
        ob_end_clean();
        return $this;
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function forceClose(): static
    {
        if(!$this->isOpen()) {
            throw new RenderException("Cannot force to close output context '{$this->name}' since it was not opened.");
        }
        
        $oldLevel = null;
        while(ob_get_level() >= $this->level && ob_get_level() > 0 && $oldLevel !== ob_get_level()) {
            $oldLevel = ob_get_level();
            ob_end_clean();
        }
        return $this;
    }
    
    public function isClosed(): bool
    {
        return $this->output !== null;
    }
    
    /**
     * @return string
     * @throws RenderException
     */
    public function getOutput(): string
    {
        if(!$this->isClosed()) {
            throw new RenderException("Cannot get output from output context '{$this->name}' since it was never closed.");
        }
        
        return $this->output;
    }
}