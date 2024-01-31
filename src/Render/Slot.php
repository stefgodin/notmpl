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
    
    private Slot|null $replacedBySlot;
    private Slot|null $replacingSlot;
    
    public function __construct(
        private readonly OutputBufferStack $obStack,
        private readonly string            $name,
    )
    {
        $this->id = uniqid("{$name}_");
        $this->replacedBySlot = null;
        $this->replacingSlot = null;
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
    
    public function isStarted(): bool
    {
        return $this->ob->wasOpened();
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
    public function getOutput(): string
    {
        return $this->replacedBySlot?->getOutput() ?? $this->ob->getOutput();
    }
    
    /**
     * @return string
     * @throws RenderException
     */
    public function getParentOutput(): string
    {
        return $this->isReplacing() ? $this->replacingSlot->getParentOutput() : $this->ob->getOutput();
    }
    
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
    
    /**
     * @param Slot $slot
     * @return $this
     * @throws RenderException
     */
    public function replaceWith(Slot $slot): static
    {
        $this->replacedBySlot = $slot;
        $slot->replacingSlot = $this;
        return $this;
    }
    
    public function isReplaced(): bool
    {
        return $this->replacedBySlot !== null;
    }
    
    public function isReplacing(): bool
    {
        return $this->replacingSlot !== null;
    }
}