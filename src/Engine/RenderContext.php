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
use Throwable;

class RenderContext
{
    private NodeTreeBuilder|null $nodeTreeBuilder;
    
    public function __construct(
        private readonly FileManager $fileManager,
        private readonly array       $globalParams,
    )
    {
        $this->nodeTreeBuilder = null;
    }
    
    /**
     * @param string $name
     * @param array $params
     * @return string
     * @throws EngineException
     * @throws Throwable
     */
    public function render(string $name, array $params = []): string
    {
        if($this->nodeTreeBuilder !== null) {
            throw new EngineException("Cannot reuse same rendering context");
        }
        
        $this->nodeTreeBuilder = new NodeTreeBuilder();
        
        try {
            $out = $this->nodeTreeBuilder
                ->capture(fn() => $this->fileManager->handle($name, array_merge($this->globalParams, $params)))
                ->buildTree()
                ->render();
            $this->nodeTreeBuilder = null;
            return $out;
        } catch(Throwable $e) {
            $this->nodeTreeBuilder->stopCapture(true);
            $this->nodeTreeBuilder = null;
            throw $e;
        }
    }
    
    /**
     * @param string $name
     * @param array $params
     * @return EnderInterface
     * @throws EngineException
     */
    public function component(string $name, array $params): EnderInterface
    {
        $this->getNodeTree()
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
        $this->getNodeTree()
            ->exitNode(UseComponentNode::getType())
            ->startCapture();
    }
    
    /**
     * @param string $name
     * @param array $bindings
     * @return EnderInterface
     * @throws EngineException
     */
    public function slot(string $name, array $bindings = []): EnderInterface
    {
        $this->getNodeTree()
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
        $this->getNodeTree()
            ->exitNode(SlotNode::getType())
            ->startCapture();
    }
    
    /**
     * @param string $name
     * @param mixed|array &$bindings
     * @return EnderInterface
     * @throws EngineException
     */
    public function useSlot(string $name, mixed &$bindings = null): EnderInterface
    {
        $this->getNodeTree()
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
        $this->getNodeTree()
            ->addNode(new ParentSlotNode())
            ->startCapture();
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function useSlotEnd(): void
    {
        $this->getNodeTree()
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
        $useComponent = $this->getNodeTree()->getCurrentNode();
        if(!$useComponent instanceof UseComponentNode) {
            return;
        }
        
        $slotCount = $useComponent->getComponent()->getSlotCount($name) ?? 0;
        $useCount = $useComponent->getLastUseSlotIndex($name) + 1;
        while($useCount < $slotCount) {
            $this->useSlot($name, $bindings);
            yield $useCount => $bindings;
            $this->useSlotEnd();
            $useCount++;
        }
    }
    
    /**
     * @param string $name
     * @return bool
     * @throws EngineException
     */
    function hasSlot(string $name = ComponentNode::DEFAULT_SLOT): bool
    {
        $useComponent = $this->getNodeTree()->getCurrentNode();
        if(!$useComponent instanceof UseComponentNode) {
            return false;
        }
        
        return $useComponent->getComponent()->getSlotCount($name) > ($useComponent->getLastUseSlotIndex($name) + 1);
    }
    
    /**
     * @return NodeTreeBuilder
     * @throws EngineException
     */
    private function getNodeTree(): NodeTreeBuilder
    {
        if($this->nodeTreeBuilder === null) {
            EngineException::throwNoContext("Rendering context is closed");
        }
        
        return $this->nodeTreeBuilder;
    }
}