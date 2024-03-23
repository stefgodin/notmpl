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

class UseComponentNode implements NodeInterface, ChildNodeInterface, ParentNodeInterface, StateListenerInterface
{
    use ChildNodeTrait;
    use TypeTrait;
    
    private UseSlotNode $implicitUseSlot;
    private bool $implicitEmpty;
    
    public function __construct(
        private readonly ComponentNode $component,
    )
    {
        $component->setUseComponent($this);
        $this->implicitUseSlot = new UseSlotNode(ComponentNode::DEFAULT_SLOT);
        $this->implicitUseSlot->setParent($this);
        $this->implicitEmpty = true;
    }
    
    public function getComponent(): ComponentNode
    {
        return $this->component;
    }
    
    public function addChild(ChildNodeInterface $node): void
    {
        if($node instanceof UseSlotNode) {
            $this->component->addUseSlot($node);
        } else {
            $this->implicitEmpty = $this->implicitEmpty && $node instanceof RawContentNode && trim($node->render()) === '';
            $this->implicitUseSlot->addChild($node);
        }
    }
    
    public function getImplicitUseSlot(): UseSlotNode|null
    {
        return !$this->implicitEmpty ? $this->implicitUseSlot : null;
    }
    
    public function getChildren(): array
    {
        return [];
    }
    
    public function onOpen(): void {}
    
    public function onClose(): void
    {
        if($this->implicitEmpty) {
            return;
        }
        
        $slots = $this->component->getSlots($this->implicitUseSlot->getSlotName());
        if(empty(array_filter($slots, fn(SlotNode $s) => $s->isReplaced()))) {
            $this->component->addUseSlot($this->implicitUseSlot);
        }
    }
    
    public function render(): string
    {
        return '';
    }
}