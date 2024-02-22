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
use StefGodin\NoTmpl\Engine\Node\ParentSlotNode;
use StefGodin\NoTmpl\Engine\Node\SlotNode;
use StefGodin\NoTmpl\Engine\Node\UseComponentNode;
use StefGodin\NoTmpl\Engine\Node\UseSlotNode;
use Throwable;

class RenderContext
{
    private NodeTreeBuilder|null $nodeTreeBuilder;
    private ScopeManager $scopeManager;
    
    public function __construct(
        private readonly FileManager $fileManager,
        private readonly array       $globalParams,
    )
    {
        $this->nodeTreeBuilder = null;
        $this->scopeManager = new ScopeManager();
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
            $this->getContentTree()
                ->capture(fn() => $this->fileManager->handle($name, array_merge($this->globalParams, $params)))
                ->stopCapture();
            
            $rootNode = $this->getContentTree()->buildTree();
            
            return $rootNode->render();
        } catch(Throwable $e) {
            $this->nodeTreeBuilder?->stopCapture(true);
            throw $e;
        }
    }
    
    /**
     * @param string $name
     * @param array $params
     * @return NodeEnder
     * @throws EngineException
     */
    public function component(string $name, array $params): NodeEnder
    {
        $ct = $this->getContentTree();
        $this->scopeManager->startNamespace();
        $component = new ComponentNode();
        $ct->addNode($component)
            ->capture(fn() => $this->fileManager->handle($name, array_merge($this->globalParams, $params)))
            ->exitNode($component);
        $this->scopeManager->useNamespace();
        $ct->addNode(new UseComponentNode($component))
            ->startCapture();
        
        return new NodeEnder($this->componentEnd(...));
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function componentEnd(): void
    {
        $this->getContentTree()
            ->exitNode(UseComponentNode::getType())
            ->startCapture();
        $this->scopeManager->endNamespace();
    }
    
    /**
     * @param string $name
     * @param array $bindings
     * @return NodeEnder
     * @throws EngineException
     */
    public function slot(string $name, array $bindings = []): NodeEnder
    {
        $slot = new SlotNode($name);
        $this->getContentTree()
            ->addNode($slot)
            ->startCapture();
        $this->scopeManager->defineScope($name, $bindings);
        
        return new NodeEnder($this->slotEnd(...));
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function slotEnd(): void
    {
        $this->getContentTree()
            ->exitNode(SlotNode::getType())
            ->startCapture();
    }
    
    /**
     * @param string $name
     * @param mixed|array &$bindings
     * @return NodeEnder
     * @throws EngineException
     */
    public function useSlot(string $name, mixed &$bindings = null): NodeEnder
    {
        $useSlot = new UseSlotNode($name);
        $this->getContentTree()
            ->addNode($useSlot)
            ->startCapture();
        $this->scopeManager->useScope($name, $bindings);
        
        return new NodeEnder($this->useSlotEnd(...));
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function parentSlot(): void
    {
        $this->getContentTree()
            ->addNode(new ParentSlotNode())
            ->startCapture();
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function useSlotEnd(): void
    {
        $this->getContentTree()
            ->exitNode(UseSlotNode::getType())
            ->startCapture();
        $this->scopeManager->leaveScope();
    }
    
    /**
     * @return NodeTreeBuilder
     * @throws EngineException
     */
    private function getContentTree(): NodeTreeBuilder
    {
        if($this->nodeTreeBuilder === null) {
            throw new EngineException("Rendering context is closed", EngineException::NO_CONTEXT);
        }
        
        return $this->nodeTreeBuilder;
    }
}