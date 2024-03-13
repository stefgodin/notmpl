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
    
    /**
     * @param ParentNodeInterface $node
     * @return void
     * @throws \StefGodin\NoTmpl\Engine\EngineException
     */
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
            $name = $node->getSlotName();
            if(empty($this->useSlots)) {
                $this->useSlots = [];
            }
            
            $this->useSlots[$name][] = $node;
        } else {
            $this->defaultUseSlot->addChild($node);
        }
    }
    
    public function getChildren(): array
    {
        return array_values(array_merge(
            [ComponentNode::DEFAULT_SLOT => $this->defaultUseSlot],
            $this->useSlots,
        ));
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
        
        return array_key_last($this->useSlots[$name] ?? []) ?? -1;
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