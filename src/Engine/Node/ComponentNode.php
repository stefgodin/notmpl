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
        if($node->getComponent() === $this) {
            $name = $node->getName();
            if(empty($this->slots[$name])) {
                $this->slots[$name] = [];
            }
            $this->slots[$name][] = $node;
        }
    }
    
    public function getSlot(string $name, int $index): SlotNode|null
    {
        return $this->slots[$name][$index] ?? null;
    }
    
    public function setUseComponent(UseComponentNode $node): void
    {
        if($node->getComponent() === $this) {
            $this->useComponent = $node;
        }
    }
    
    public function getUseSlot(SlotNode $slot): UseSlotNode|null
    {
        $index = array_search($slot, $this->slots[$slot->getName()], true);
        return $index !== false ? $this->useComponent?->getUseSlot($slot->getName(), $index) : null;
    }
    
    public function getSlotCount(string $name): int
    {
        return count($this->slots[$name] ?? []);
    }
}