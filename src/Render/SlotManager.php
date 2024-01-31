<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderException;

class SlotManager
{
    /** @var Slot[] */
    private array $slots;
    
    public function __construct(
        private readonly OutputBufferStack $obStack,
    )
    {
        $this->slots = [];
    }
    
    /**
     * @param string $name
     * @return void
     * @throws RenderException
     */
    public function startSlot(string $name): void
    {
        $slot = new Slot($this->obStack, $name);
        $oldSlot = $this->getFirstSlotWithName($name);
        if($oldSlot) {
            if(!$oldSlot->isEnded()) {
                throw new RenderException("Cannot overwrite non-ended slot '{$name}' within '{$this->obStack->getCurrent()->getName()}'.");
            }
            
            $oldSlot->replaceWith($slot);
        } else {
            $this->obStack->getCurrent()->writeContent($slot->getMarkup());
        }
        
        $this->slots[] = $slot;
        $slot->start();
    }
    
    /**
     * @return void
     * @throws RenderException
     */
    public function renderParentSlot(): void
    {
        $openSlot = $this->getLastOpenSlot();
        if(!$openSlot) {
            throw new RenderException("There is no parent slot to render within '{$this->obStack->getCurrent()->getName()}'.");
        }
        
        $parentSlot = $this->getFirstSlotWithName($openSlot->getName());
        if(!$parentSlot) {
            throw new RenderException("Slot '{$openSlot->getName()}' is not extending a parent slot within '{$this->obStack->getCurrent()->getName()}'.");
        }
        
        $this->obStack->getCurrent()
            ->writeContent($parentSlot->getOriginalOutput());
    }
    
    /**
     * @return void
     * @throws RenderException
     */
    public function endSlot(): void
    {
        $openSlot = $this->getLastOpenSlot();
        if(!$openSlot) {
            throw new RenderException("There are no more slots to end within '{$this->obStack->getCurrent()->getName()}'.");
        }
        
        $openSlot->end();
    }
    
    public function processSlotContent(string $output): string
    {
        $slotContent = array_combine(
            array_map(fn(Slot $slot) => $slot->getMarkup(), $this->slots),
            array_map(fn(Slot $slot) => $this->getLastSlotWithName($slot->getName())->getOriginalOutput(), $this->slots),
        );
        
        do {
            // Maybe prevent content recursion somehow?
            $newOutput = strtr($output, $slotContent);
            $outputChanged = $newOutput !== $output;
            $output = $newOutput;
        } while($outputChanged);
        
        return $output;
    }
    
    private function getFirstSlotWithName(string $name): Slot|null
    {
        foreach($this->slots as $slot) {
            if($slot->getName() === $name) {
                return $slot;
            }
        }
        
        return null;
    }
    
    private function getLastSlotWithName(string $name): Slot|null
    {
        foreach(array_reverse($this->slots) as $slot) {
            if($slot->getName() === $name) {
                return $slot;
            }
        }
        
        return null;
    }
    
    private function getLastOpenSlot(): Slot|null
    {
        foreach(array_reverse($this->slots) as $slot) {
            if(!$slot->isEnded()) {
                return $slot;
            }
        }
        
        return null;
    }
}