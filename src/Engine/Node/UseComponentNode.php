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
            $this->useSlots[$node->getSlotName()] = $node;
        } else {
            $this->defaultUseSlot->addChild($node);
            $node->setParent($this->defaultUseSlot);
        }
    }
    
    public function getChildren(): array
    {
        return array_values(array_merge(
            [ComponentNode::DEFAULT_SLOT => $this->defaultUseSlot],
            $this->useSlots,
        ));
    }
    
    public function getUseSlot(string $name): UseSlotNode|null
    {
        return $this->useSlots[$name] ?? ($name === ComponentNode::DEFAULT_SLOT ? $this->defaultUseSlot : null);
    }
    
    public function render(): string
    {
        return '';
    }
}