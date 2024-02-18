<?php


namespace StefGodin\NoTmpl\Engine;

use Throwable;

class RenderContext
{
    private ContentTreeBuilder|null $contentTreeBuilder;
    private ScopeManager $scopeManager;
    
    public function __construct(
        private readonly FileManager $fileManager,
        private readonly array       $globalParams,
    )
    {
        $this->contentTreeBuilder = null;
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
        if($this->contentTreeBuilder !== null) {
            throw new EngineException("Cannot reuse same rendering context");
        }
        
        $this->contentTreeBuilder = new ContentTreeBuilder($name);
        
        try {
            $this->getContentTree()
                ->capture(fn() => $this->fileManager->handle($name, array_merge($this->globalParams, $params)))
                ->stopCapture();
            
            $contentTree = $this->getContentTree()->buildContentTree();
            
            $processor = new ContentTreeProcessor();
            return $processor->processTree($contentTree);
        } catch(Throwable $e) {
            $this->contentTreeBuilder?->stopCapture(true);
            throw $e;
        }
    }
    
    public function component(string $name, array $params)
    {
        $this->getContentTree()
            ->openTag("component", $name)
            ->openTag("component_internal", $name);
        
        $this->scopeManager->startNamespace();
        $this->getContentTree()
            ->capture(fn() => $this->fileManager->handle($name, array_merge($this->globalParams, $params)))
            ->closeTag("component_internal")
            ->openTag("component_external", $name)
            ->startCapture();
        $this->scopeManager->useNamespace();
    }
    
    public function componentEnd()
    {
        $this->getContentTree()
            ->closeTag("component_external")
            ->closeTag("component")
            ->startCapture();
        $this->scopeManager->endNamespace();
    }
    
    public function slot(string $name, array $bindings = [])
    {
        $this->getContentTree()
            ->openTag("slot", $name)
            ->startCapture();
        $this->scopeManager->defineScope($name, $bindings);
    }
    
    public function slotEnd()
    {
        $this->getContentTree()
            ->closeTag("slot")
            ->startCapture();
        //        $this->scopeManager->endScopeDefine();
    }
    
    public function useSlot(string $name, mixed &$bindings = null)
    {
        $this->getContentTree()
            ->openTag("use_slot", $name)
            ->startCapture();
        $this->scopeManager->useScope($name, $bindings);
    }
    
    public function parentSlot()
    {
        $this->getContentTree()
            ->openTag("parent_slot", "parent_slot")
            ->closeTag("parent_slot")
            ->startCapture();
    }
    
    public function useSlotEnd()
    {
        $this->getContentTree()
            ->closeTag("use_slot")
            ->startCapture();
        $this->scopeManager->resetUseScope();
    }
    
    private function getContentTree(): ContentTreeBuilder
    {
        if($this->contentTreeBuilder === null) {
            throw new EngineException("Rendering context is closed", EngineException::NO_CONTEXT);
        }
        
        return $this->contentTreeBuilder;
    }
}