<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderException;

/**
 * @internal
 */
class RenderBlock
{
    private string $id;
    private OutputContext $outputContext;
    
    private RenderBlock|null $replacedByBlock;
    private RenderBlock|null $replacingBlock;
    
    public function __construct(
        private string $name,
    )
    {
        $this->id = uniqid("{$name}_");
        $this->replacedByBlock = null;
        $this->replacingBlock = null;
        $this->outputContext = OutputContext::create("Block {$this->name} {$this->getMarkup()}");
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
     * @return $this
     * @throws RenderException
     */
    public function start(): static
    {
        $this->outputContext->open();
        return $this;
    }
    
    public function isEnded(): bool
    {
        return $this->outputContext->isClosed();
    }
    
    /**
     * @return string
     * @throws RenderException
     */
    public function getOutput(): string
    {
        return $this->replacedByBlock?->getOutput() ?? $this->outputContext->getOutput();
    }
    
    /**
     * @return string
     * @throws RenderException
     */
    public function getParentOutput(): string
    {
        return $this->isReplacing() ? $this->replacingBlock->getParentOutput() : $this->outputContext->getOutput();
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function end(): static
    {
        $this->outputContext->close();
        return $this;
    }
    
    /**
     * @param RenderBlock $block
     * @return $this
     * @throws RenderException
     */
    public function replaceWith(RenderBlock $block): static
    {
        if($block === $this) {
            throw new RenderException("Cannot replace block '{$block->name}' by itself.");
        }
        
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