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
    
    /** @var SlotNode[] */
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
            $this->slots[$node->getName()] = $node;
        }
    }
    
    public function getSlot(string $slotName = self::DEFAULT_SLOT): SlotNode|null
    {
        return $this->slots[$slotName] ?? null;
    }
    
    public function setUseComponent(UseComponentNode $node): void
    {
        if($node->getComponent() === $this) {
            $this->useComponent = $node;
        }
    }
    
    public function getUseSlot(string $slotName = self::DEFAULT_SLOT): UseSlotNode|null
    {
        return $this->useComponent?->getUseSlot($slotName);
    }
}