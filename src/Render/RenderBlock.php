<?php


namespace Stefmachine\NoTmpl\Render;

/**
 * @internal
 */
class RenderBlock
{
    protected string $id;
    protected OutputContext $outputContext;
    
    protected RenderBlock|null $replacedByBlock;
    protected RenderBlock|null $replacingBlock;
    
    public function __construct(
        protected string $name,
    )
    {
        $this->id = uniqid("{$name}_");
        $this->replacedByBlock = null;
        $this->replacingBlock = null;
        $this->outputContext = new OutputContext("Block {$this->name} {$this->getMarkup()}");
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
        return isset($this->obLevel);
    }
    
    /**
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public function start(): void
    {
        $this->outputContext->open();
    }
    
    public function isEnded(): bool
    {
        return $this->outputContext->isClosed();
    }
    
    /**
     * @return string
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public function getOutput(): string
    {
        return $this->replacedByBlock?->getOutput() ?? $this->outputContext->getOutput();
    }
    
    /**
     * @return $this
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public function end(): static
    {
        $this->outputContext->close();
        return $this;
    }
    
    public function replaceWith(RenderBlock $block): static
    {
        $this->replacedByBlock = $block;
        $block->replacingBlock = $this;
        return $this;
    }
    
    public function isReplaced(): bool
    {
        return $this->replacedByBlock !== null;
    }
    
    public function isReplacing(): bool
    {
        return $this->replacingBlock !== null;
    }
}