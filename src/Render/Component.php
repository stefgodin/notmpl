<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Config\ConfigInjectTrait;
use Stefmachine\NoTmpl\Exception\RenderException;

/**
 * @internal
 */
class Component
{
    use ConfigInjectTrait;
    
    private readonly SlotManager $slotManager;
    private readonly OutputBuffer $ob;
    private readonly OutputBufferStack $obStack;
    private bool $rendering;
    
    public function __construct(
        private readonly ComponentStack $componentStack,
        private readonly string         $template,
        private readonly array          $params = [],
        OutputBufferStack|null          $obStack = null,
    )
    {
        $this->obStack = $obStack ?? new OutputBufferStack();
        $this->ob = new OutputBuffer($this->obStack, "component:{$this->template}");
        $this->slotManager = new SlotManager($this->obStack);
        $this->rendering = false;
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function start(): static
    {
        $this->componentStack->push($this);
        $this->ob->open();
        $this->rendering = true;
        $this->ob->includeFile(
            $this->template,
            array_merge($this->getConfig()->getRenderGlobalParams(), $this->params),
        );
        $this->rendering = false;
        $this->slotManager->lockCreation();
        return $this;
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function end(): static
    {
        if($this->rendering) {
            throw new RenderException("Cannot close '{$this->ob->getName()}' while it is still rendering.");
        }
        
        $this->ob->close();
        $this->componentStack->pop();
        
        if(!$this->obStack->isEmpty()) {
            $this->obStack->getCurrent()->writeContent($this->getOutput());
        }
        
        return $this;
    }
    
    /**
     * @return string
     * @throws RenderException
     */
    public function getOutput(): string
    {
        $output = $this->ob->getOutput();
        return $this->slotManager->processSlotContent($output);
    }
    
    /**
     * @param string $name
     * @return $this
     * @throws RenderException
     */
    public function startSlot(string $name): static
    {
        $this->slotManager->startSlot($name);
        return $this;
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function renderParentSlot(): static
    {
        $this->slotManager->renderParentSlot();
        return $this;
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function endSlot(): static
    {
        $this->slotManager->endSlot();
        return $this;
    }
    
    public function component(string $template, array $params = []): Component
    {
        return new Component(
            $this->componentStack,
            $template,
            array_merge($this->params, $params),
            $this->obStack
        );
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function cleanUp(): static
    {
        if($this->ob->isOpen()) {
            $this->ob->forceClose();
        }
        while($this->componentStack->has($this)) {
            $current = $this->componentStack->getCurrent();
            if($current === $this) {
                $this->componentStack->pop();
            } else {
                $current->cleanUp();
            }
        }
        
        return $this;
    }
}