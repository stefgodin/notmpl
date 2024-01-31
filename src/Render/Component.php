<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Config\ConfigInjectTrait;

class Component
{
    use ConfigInjectTrait;
    
    private readonly SlotManager $slotManager;
    private readonly OutputBuffer $ob;
    private readonly OutputBufferStack $obStack;
    
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
    }
    
    public function start(): static
    {
        $this->componentStack->push($this);
        $this->ob->open();
        $this->ob->includeFile(
            $this->template,
            array_merge($this->getConfig()->getRenderGlobalParams(), $this->params),
        );
        return $this;
    }
    
    public function isStarted(): bool
    {
        return $this->ob->wasOpened();
    }
    
    public function end(): static
    {
        $this->ob->close();
        $this->componentStack->pop();
        
        if($this->obStack->hasBuffer()) {
            $this->obStack->getCurrent()->writeContent($this->getOutput());
        }
        
        return $this;
    }
    
    public function isEnded(): bool
    {
        return $this->ob->isClosed();
    }
    
    public function getOutput(): string
    {
        $output = $this->ob->getOutput();
        return $this->slotManager->processSlotContent($output);
    }
    
    public function startSlot(string $name): static
    {
        $this->slotManager->startSlot($name);
        return $this;
    }
    
    public function renderParentSlot(): static
    {
        $this->slotManager->renderParentSlot();
        return $this;
    }
    
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