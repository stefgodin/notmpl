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

class UseComponentNode implements NodeInterface, ChildNodeInterface, ParentNodeInterface
{
    use ChildNodeTrait;
    use TypeTrait;
    
    private UseSlotNode $defaultUseSlot;
    private array $useSlots;
    
    public function __construct(
        private readonly ComponentNode $component,
    )
    {
        $component->setUseComponent($this);
        $this->useSlots = [];
    }
    
    public function setParent(ParentNodeInterface $node): void
    {
        $this->parent = $node;
        $this->defaultUseSlot = new UseSlotNode();
        $this->defaultUseSlot->setParent($this);
    }
    
    public function getComponent(): ComponentNode
    {
        return $this->component;
    }
    
    public function addChild(ChildNodeInterface $node): void
    {
        if($node instanceof UseSlotNode) {
            $this->useSlots[$node->getSlotName()][] = $node;
        } else {
            $node->setParent($this->defaultUseSlot);
            $this->defaultUseSlot->addChild($node);
        }
    }
    
    public function getChildren(): array
    {
        return [];
    }
    
    public function getUseSlot(string $name, int $index): UseSlotNode|null
    {
        if($index === 0 && empty($this->useSlots[$name]) && $name === ComponentNode::DEFAULT_SLOT) {
            return $this->defaultUseSlot;
        }
        
        return $this->useSlots[$name][$index] ?? null;
    }
    
    public function getLastUseSlotIndex(string $name): int
    {
        if(empty($this->useSlots[$name]) && $name === ComponentNode::DEFAULT_SLOT) {
            return 0;
        }
        
        return empty($this->useSlots[$name]) ? -1 : array_key_last($this->useSlots[$name]);
    }
    
    public function getUseSlotIndex(UseSlotNode $node): int
    {
        if($node === $this->defaultUseSlot) {
            return empty($this->useSlots[ComponentNode::DEFAULT_SLOT]) ? 0 : -1;
        }
        
        $index = array_search($node, $this->useSlots[$node->getSlotName()] ?? []);
        return $index !== false ? $index : -1;
    }
    
    public function render(): string
    {
        return '';
    }
}