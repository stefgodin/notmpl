<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderException;

/**
 * @internal
 */
class Slot
{
    private string $id;
    private OutputBuffer $ob;
    
    public function __construct(
        private readonly OutputBufferStack $obStack,
        private readonly string            $name,
    )
    {
        $this->id = uniqid("{$name}_");
        $this->ob = new OutputBuffer($this->obStack, "slot:{$this->name}-{$this->id}");
    }
    
    public function getMarkup(): string
    {
        return "<{$this->id}>";
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function start(): static
    {
        $this->ob->open();
        return $this;
    }
    
    public function isEnded(): bool
    {
        return $this->ob->isClosed();
    }
    
    /**
     * @return string
     * @throws RenderException
     */
    public function getOriginalOutput(): string
    {
        return $this->ob->getOutput();
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function end(): static
    {
        $this->ob->close();
        return $this;
    }
}