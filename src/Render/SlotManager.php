<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderError;
use Stefmachine\NoTmpl\Exception\RenderException;

/**
 * @internal
 */
class SlotManager
{
    /** @var Slot[] */
    private array $slots;
    private bool $creationLocked;
    
    public function __construct(
        private readonly OutputBufferStack $obStack,
    )
    {
        $this->slots = [];
        $this->creationLocked = false;
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
        if($oldSlot && !$oldSlot->isEnded()) {
            throw new RenderException(
                "Cannot overwrite non-ended slot '{$name}' within '{$this->obStack->getCurrent()->getName()}'.",
                RenderError::SLOTMAN_OPEN_SLOT_OVERWRITE
            );
        }
        
        if(!$oldSlot) {
            if($this->creationLocked) {
                throw new RenderException(
                    "Cannot overwrite non-existent slot '{$name}'.",
                    RenderError::SLOTMAN_UNDEFINED_SLOT_OVERWRITE
                );
            }
            
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
            throw new RenderException(
                "There is no parent slot to render within '{$this->obStack->getCurrent()->getName()}'.",
                RenderError::SLOTMAN_NO_CHILD_SLOT
            );
        }
        
        $parentSlot = $this->getFirstSlotWithName($openSlot->getName());
        if(!$parentSlot) {
            throw new RenderException(
                "Slot '{$openSlot->getName()}' is not extending a parent slot within '{$this->obStack->getCurrent()->getName()}'.",
                RenderError::SLOTMAN_NO_PARENT_SLOT
            );
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
            throw new RenderException(
                "There are no more slots to end within '{$this->obStack->getCurrent()->getName()}'.",
                RenderError::SLOTMAN_NO_OPEN_SLOT
            );
        }
        
        $openSlot->end();
    }
    
    /**
     * @param string $output
     * @return string
     * @throws RenderException
     */
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
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function lockCreation(): static
    {
        foreach($this->slots as $slot) {
            if(!$slot->isEnded()) {
                throw new RenderException(
                    "Cannot lock slot creation while some slots are still opened.",
                    RenderError::SLOTMAN_SLOTS_STILL_OPENED
                );
            }
        }
        $this->creationLocked = true;
        return $this;
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