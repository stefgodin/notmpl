<?php
/*
 * This file is part of the NoTMPL package.
 *
 * (c) Stéphane Godin
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */


namespace StefGodin\NoTmpl\Engine;

use Generator;
use StefGodin\NoTmpl\Engine\Node\ComponentNode;
use StefGodin\NoTmpl\Engine\Node\ParentSlotNode;
use StefGodin\NoTmpl\Engine\Node\SlotNode;
use StefGodin\NoTmpl\Engine\Node\UseComponentNode;
use StefGodin\NoTmpl\Engine\Node\UseSlotNode;

class RenderContext
{
    private NodeTreeBuilder $nodeTreeBuilder;
    
    public function __construct(
        private readonly FileManager $fileManager,
        private readonly array       $globalParams,
    )
    {
        $this->nodeTreeBuilder = new NodeTreeBuilder();
    }
    
    public function render(): string
    {
        return $this->nodeTreeBuilder->stopCapture()->buildTree()->render();
    }
    
    public function cleanup(): void
    {
        $this->nodeTreeBuilder->stopCapture(true);
    }
    
    /**
     * @param string $name
     * @param array $params
     * @return NodeEnder
     * @throws EngineException
     */
    public function component(string $name, array $params): NodeEnder
    {
        $this->nodeTreeBuilder
            ->addNode($component = new ComponentNode())
            ->capture(fn() => $this->fileManager->handle($name, array_merge($this->globalParams, $params)))
            ->exitNode($component)
            ->addNode(new UseComponentNode($component))
            ->startCapture();
        
        return new NodeEnder($this->componentEnd(...));
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function componentEnd(): void
    {
        $this->nodeTreeBuilder
            ->exitNode(UseComponentNode::getType())
            ->startCapture();
    }
    
    /**
     * @param string $name
     * @param array $bindings
     * @return NodeEnder
     * @throws EngineException
     */
    public function slot(string $name = ComponentNode::DEFAULT_SLOT, array $bindings = []): NodeEnder
    {
        $this->nodeTreeBuilder
            ->addNode(new SlotNode($name, $bindings))
            ->startCapture();
        
        return new NodeEnder($this->slotEnd(...));
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function slotEnd(): void
    {
        $this->nodeTreeBuilder
            ->exitNode(SlotNode::getType())
            ->startCapture();
    }
    
    /**
     * @param string $name
     * @param mixed|array &$bindings
     * @return NodeEnder
     * @throws EngineException
     */
    public function useSlot(string $name = ComponentNode::DEFAULT_SLOT, mixed &$bindings = null): NodeEnder
    {
        $this->nodeTreeBuilder
            ->addNode(new UseSlotNode($name, $bindings))
            ->startCapture();
        
        return new NodeEnder($this->useSlotEnd(...));
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function parentSlot(): void
    {
        $this->nodeTreeBuilder
            ->addNode(new ParentSlotNode())
            ->startCapture();
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function useSlotEnd(): void
    {
        $this->nodeTreeBuilder
            ->exitNode(UseSlotNode::getType())
            ->startCapture();
    }
    
    /**
     * @param string $name
     * @return Generator
     * @throws EngineException
     */
    public function useRepeatSlots(string $name = ComponentNode::DEFAULT_SLOT): Generator
    {
        $useComponent = $this->nodeTreeBuilder->getCurrentNode();
        if(!$useComponent instanceof UseComponentNode) {
            return;
        }
        
        foreach($useComponent->getComponent()->getSlots($name) as $i => $slot) {
            if(!$slot->isReplaced()) {
                $this->useSlot($name, $bindings);
                yield $i => $bindings;
                $this->useSlotEnd();
            }
        }
    }
    
    /**
     * @param string $name
     * @return bool
     * @throws EngineException
     */
    function hasSlot(string $name = ComponentNode::DEFAULT_SLOT): bool
    {
        $useComponent = $this->nodeTreeBuilder->getCurrentNode();
        if(!$useComponent instanceof UseComponentNode) {
            return false;
        }
        
        return !empty(array_filter($useComponent->getComponent()->getSlots($name), fn(SlotNode $s) => !$s->isReplaced()));
    }
}