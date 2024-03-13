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

use StefGodin\NoTmpl\Engine\Node\ComponentNode;
use StefGodin\NoTmpl\Engine\Node\NodeHelper;
use StefGodin\NoTmpl\Engine\Node\NodeInterface;
use StefGodin\NoTmpl\Engine\Node\ParentSlotNode;
use StefGodin\NoTmpl\Engine\Node\SlotNode;
use StefGodin\NoTmpl\Engine\Node\UseComponentNode;
use StefGodin\NoTmpl\Engine\Node\UseSlotNode;
use Throwable;
use Traversable;

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
            $this->getNodeTree()
                ->capture(fn() => $this->fileManager->handle($name, array_merge($this->globalParams, $params)));
            
            $rootNode = $this->getNodeTree()->buildTree();
            
            return $rootNode->render();
        } catch(Throwable $e) {
            $this->nodeTreeBuilder?->stopCapture(true);
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
    
    public function useRepeatSlots(string $name): Traversable&EnderInterface
    {
        $node = $this->nodeTreeBuilder->getCurrentNode();
        /** @var UseComponentNode|null $useComponent */
        $useComponent = NodeHelper::climbUntil($node, fn(NodeInterface $n) => $n instanceof UseComponentNode);
        $startIndex = ($useComponent?->getLastUseSlotIndex($name) ?? -2) + 1;
        $endIndex = ($useComponent?->getComponent()->getSlotCount($name) ?? 0) - 1;
        
        return new NodeBuilderIterator(
            function() use ($name) {
                $this->useSlot($name, $bindings);
                return $bindings;
            },
            fn() => $this->useSlotEnd(),
            $startIndex,
            $endIndex
        );
    }
    
    /**
     * @return NodeTreeBuilder
     * @throws EngineException
     */
    private function getNodeTree(): NodeTreeBuilder
    {
        if($this->nodeTreeBuilder === null) {
            throw new EngineException("Rendering context is closed", EngineException::NO_CONTEXT);
        }
        
        return $this->nodeTreeBuilder;
    }
}