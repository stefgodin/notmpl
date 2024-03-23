<?php
/*
 * This file is part of the NoTMPL package.
 *
 * (c) Stéphane Godin
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */


namespace StefGodin\NoTmpl\Engine\Node;

class ComponentNode implements NodeInterface, ChildNodeInterface, ParentNodeInterface
{
    const DEFAULT_SLOT = 'default';
    
    use ChildNodeTrait;
    use ParentNodeTrait;
    use TypeTrait;
    
    /** @var SlotNode[][] */
    private array $slots;
    private UseComponentNode|null $useComponent;
    
    public function __construct()
    {
        $this->slots = [];
        $this->useComponent = null;
    }
    
    public function addSlot(SlotNode $node): void
    {
        $this->slots[$node->getName()][] = $node;
    }
    
    public function setUseComponent(UseComponentNode $node): void
    {
        $this->useComponent = $node;
    }
    
    public function addUseSlot(UseSlotNode $useSlot): void
    {
        foreach($this->slots[$useSlot->getSlotName()] ?? [] as $slot) {
            if(!$slot->isReplaced()) {
                $slot->setReplacementNode($useSlot);
                $useSlot->setSlot($slot);
                break;
            }
        }
    }
    
    public function getSlots(string $name): array
    {
        return $this->slots[$name] ?? [];
    }
}