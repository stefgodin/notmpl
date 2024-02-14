<?php


namespace StefGodin\NoTmpl\Engine;

use Throwable;

/**
 * @internal
 */
class RenderContext
{
    private const COMPONENT_TAG = 'c';
    private const SLOT_TAG = 's';
    private const USE_SLOT_TAG = 'u';
    private const OPEN_TAG = 'o';
    private const INTERNAL_TAG = 'i';
    
    protected OutputBufferList $obList;
    protected array $slotBindings;
    protected array $useSlotBindings;
    
    public function __construct(
        private readonly TemplateResolver $templateResolver,
        private readonly array            $globalParams,
    )
    {
        $this->obList = new OutputBufferList();
        $this->slotBindings = [];
        $this->useSlotBindings = [];
    }
    
    /**
     * @param string $name
     * @param array $params
     * @return string
     * @throws EngineException
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function render(string $name, array $params = []): string
    {
        try {
            $ob = $this->obList->add("render:{$name}")
                ->open();
            $this->component($name, $params);
            $this->componentEnd();
            
            $output = $ob
                ->close()
                ->getOutput();
            
            $slotContent = [];
            foreach($this->obList->all(self::isSlot()) as $slot) {
                $slotContent[$slot->getId()] = $slot->getOutput();
                $slotContent["{$slot->getId()}:parent"] = $slot->getOutput();
            }
            
            $useSlots = $this->obList->all(
                self::isUseSlot(),
                fn(OutputBuffer $ob) => !empty($ob->getTag(self::SLOT_TAG)),
            );
            foreach($useSlots as $useSlot) {
                $slotContent[$useSlot->getTag(self::SLOT_TAG)] = $useSlot->getOutput();
            }
            
            do {
                // TODO: Maybe prevent content recursion somehow?
                $newOutput = str_replace(array_keys($slotContent), array_values($slotContent), $output);
                $outputChanged = $newOutput !== $output;
                $output = $newOutput;
            } while($outputChanged);
            
            return $output;
        } catch(Throwable $e) {
            while($currentOb = $this->obList->getLast(OutputBufferList::isOpen())) {
                $currentOb->forceClose();
            }
            
            throw $e;
        }
    }
    
    /**
     * @param string $name
     * @param array $params
     * @return void
     * @throws EngineException
     */
    public function component(string $name, array $params = []): void
    {
        $parentBuffer = $this->obList->getLast(OutputBufferList::isOpen());
        if(!$parentBuffer) {
            throw new EngineException(
                "Cannot create component '{$name}' outside of a rendering context",
                EngineException::CTX_NO_CONTEXT
            );
        }
        
        $ob = $this->obList->add("component:{$name}");
        $ob->addTag(self::COMPONENT_TAG, $ob->getId());
        
        $file = $this->templateResolver->resolve($name);
        $isPhp = pathinfo($file, PATHINFO_EXTENSION) === 'php';
        
        $allParams = array_merge($this->globalParams, $params);
        
        $ob->addTag(self::OPEN_TAG)
            ->open()
            ->writeContent($isPhp ? fn() => IsolatedPhpRenderer::render($file, $allParams) : file_get_contents($file))
            ->close();
        
        $parentBuffer->writeContent($ob->getOutput());
        $this->useSlot(internal: true);
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function componentEnd(): void
    {
        $componentOb = $this->obList->getLast(
            self::isTagOpen(),
            self::isComponent(),
        );
        
        if(!$componentOb || $componentOb->isOpen()) {
            throw new EngineException(
                "There is no component to end",
                EngineException::CTX_NO_OPEN_TAG
            );
        }
        
        $ob = $this->obList->getLast(self::isTagOpen());
        if(self::isUseSlot()($ob) && $ob->hasTag(self::INTERNAL_TAG)) {
            // Close use-slot:default
            $this->useSlotEnd();
            $ob = $this->obList->getLast(self::isTagOpen());
        }
        
        if($ob !== $componentOb) {
            throw new EngineException(
                "Cannot close component '{$componentOb->getName()}' before closing '{$ob->getName()}'",
                EngineException::CTX_INVALID_OPEN_TAG
            );
        }
        
        $ob->clearTag(self::OPEN_TAG);
    }
    
    /**
     * @param string $name
     * @param array $bindings
     * @return void
     * @throws EngineException
     */
    public function slot(string $name = 'default', array $bindings = []): void
    {
        $componentOb = $this->obList->getLast(
            self::isTagOpen(),
            self::isComponent(),
            OutputBufferList::isOpen(),
        );
        
        if(!$componentOb) {
            throw new EngineException(
                "There is no rendering component to add a slot '{$name}' to",
                EngineException::CTX_INVALID_OPEN_TAG
            );
        }
        
        $existingSlot = $this->obList->getFirst(
            self::isSlot(),
            OutputBufferList::hasName("slot:{$name}"),
            OutputBufferList::hasTag(self::COMPONENT_TAG, $componentOb->getId()),
        );
        
        if($existingSlot) {
            throw new EngineException(
                "There is already a '{$name}' slot in the component '{$componentOb->getName()}'",
                EngineException::CTX_INVALID_NAME
            );
        }
        
        $ob = $this->obList->add("slot:{$name}");
        $this->obList->getLast(OutputBufferList::isOpen())
            ->writeContent($ob->getId());
        
        $ob->addTag(self::SLOT_TAG, $name)
            ->addTag(self::OPEN_TAG)
            ->addTag(self::COMPONENT_TAG, $componentOb->getId())
            ->open();
        
        $this->slotBindings[$ob->getId()] = $bindings;
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function slotEnd(): void
    {
        $slotOb = $this->obList->getLast(
            self::isTagOpen(),
            self::isSlot(),
        );
        if(!$slotOb) {
            throw new EngineException("There is no slot to close", EngineException::CTX_NO_OPEN_TAG);
        }
        
        $ob = $this->obList->getLast(self::isTagOpen());
        if($ob !== $slotOb) {
            throw new EngineException(
                "Cannot close slot '{$slotOb->getName()}' before closing '{$ob->getName()}'",
                EngineException::CTX_INVALID_OPEN_TAG
            );
        }
        
        $slotOb->close()
            ->clearTag(self::OPEN_TAG);
    }
    
    /**
     * @param string $name
     * @param mixed $bindings
     * @param bool $internal
     * @return void
     * @throws EngineException
     */
    public function useSlot(string $name = 'default', mixed &$bindings = null, bool $internal = false): void
    {
        $componentOb = $this->obList->getLast(
            self::isTagOpen(),
            self::isComponent(),
            OutputBufferList::isClosed(),
        );
        if(!$componentOb) {
            throw new EngineException(
                "There is no component to use a slot '{$name}' from",
                EngineException::CTX_INVALID_OPEN_TAG
            );
        }
        
        $parentSlot = $this->obList->getFirst(
            self::isSlot(),
            OutputBufferList::hasTag(self::COMPONENT_TAG, $componentOb->getId()),
            OutputBufferList::hasName("slot:{$name}"),
        );
        
        if($parentSlot) {
            $existingUseSlot = $this->obList->getLast(
                self::isUseSlot(),
                OutputBufferList::hasTag(self::COMPONENT_TAG, $componentOb->getId()),
                OutputBufferList::hasTag(self::SLOT_TAG, $parentSlot->getId()),
                fn($ob) => !OutputBufferList::hasTag(self::INTERNAL_TAG)($ob),
            );
            
            if($existingUseSlot) {
                throw new EngineException(
                    "There is already a '{$name}' use-slot in the component '{$componentOb->getName()}'",
                    EngineException::CTX_INVALID_NAME
                );
            }
        }
        
        $expectedOb = $componentOb;
        if(!$internal) {
            $expectedOb = $this->obList->getLast(
                self::isTagOpen(),
                self::isUseSlot(),
                OutputBufferList::hasTag(self::INTERNAL_TAG),
            );
        }
        
        $topMostOb = $this->obList->getLast(self::isTagOpen());
        if($topMostOb !== $expectedOb) {
            throw new EngineException(
                "Cannot use a slot '{$name}' when not directly placed inside component '{$componentOb->getName()}'",
                EngineException::CTX_INVALID_OPEN_TAG
            );
        }
        
        $ob = $this->obList->add("use-slot:{$name}");
        
        $ob->addTag(self::USE_SLOT_TAG, $ob->getId())
            ->addTag(self::SLOT_TAG, $parentSlot?->getId() ?? '')
            ->addTag(self::COMPONENT_TAG, $componentOb->getId())
            ->addTag(self::OPEN_TAG)
            ->open();
        
        if($internal) {
            $ob->addTag(self::INTERNAL_TAG);
        }
        
        $this->useSlotBindings[$ob->getId() . ':previous'] = $bindings; // Copy data
        $this->useSlotBindings[$ob->getId()] = &$bindings; // Keep direct reference
        $bindings = []; // Cleanup for new scope
        if($parentSlot) {
            $bindings = $this->slotBindings[$parentSlot->getId()];
        }
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function parentSlot(): void
    {
        $useSlotOb = $this->obList->getLast(
            self::isTagOpen(),
            self::isUseSlot(),
        );
        
        $componentOb = $this->obList->getLast(
            self::isTagOpen(),
            self::isComponent(),
        );
        
        if(!$useSlotOb || !$componentOb || $componentOb->getId() !== $useSlotOb->getTag(self::COMPONENT_TAG)) {
            throw new EngineException(
                "Cannot render parent slot content outside of use-slot context",
                EngineException::CTX_INVALID_OPEN_TAG
            );
        }
        
        $parentSlot = $useSlotOb->getTag(self::SLOT_TAG);
        if($parentSlot) {
            $this->obList->getLast(OutputBufferList::isOpen())
                ->writeContent($useSlotOb->getTag(self::SLOT_TAG) . ':parent');
        }
    }
    
    /**
     * @return void
     * @throws EngineException
     */
    public function useSlotEnd(): void
    {
        $slotOb = $this->obList->getLast(
            self::isTagOpen(),
            self::isUseSlot(),
        );
        if(!$slotOb) {
            throw new EngineException(
                "There is no use-slot to close",
                EngineException::CTX_INVALID_OPEN_TAG
            );
        }
        
        $ob = $this->obList->getLast(self::isTagOpen());
        if($ob !== $slotOb) {
            throw new EngineException(
                "Cannot close use-slot '{$slotOb->getName()}' before closing '{$ob->getName()}'",
                EngineException::OB_INVALID_STATE
            );
        }
        
        $ob->close()
            ->clearTag(self::OPEN_TAG);
        
        // Reset referenced bindings to ensure proper scope references (clearing scope variable within template)
        $this->useSlotBindings[$ob->getId()] = $this->useSlotBindings[$ob->getId() . ':previous'];
        unset($this->useSlotBindings[$ob->getId()]);
        unset($this->useSlotBindings[$ob->getId() . ':previous']);
    }
    
    private static function isComponent(): callable
    {
        return fn(OutputBuffer $ob) => $ob->hasTag(self::COMPONENT_TAG)
            && !$ob->hasTag(self::SLOT_TAG);
    }
    
    private static function isSlot(): callable
    {
        return fn(OutputBuffer $ob) => $ob->hasTag(self::SLOT_TAG)
            && !$ob->hasTag(self::USE_SLOT_TAG);
    }
    
    private static function isUseSlot(): callable
    {
        return fn(OutputBuffer $ob) => $ob->hasTag(self::USE_SLOT_TAG);
    }
    
    private static function isTagOpen(): callable
    {
        return fn(OutputBuffer $ob) => $ob->hasTag(self::OPEN_TAG);
    }
}