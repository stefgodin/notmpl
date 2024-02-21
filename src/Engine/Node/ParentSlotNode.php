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

use StefGodin\NoTmpl\Engine\EngineException;

class ParentSlotNode implements NodeInterface, ChildNodeInterface
{
    use TypeTrait;
    use ChildNodeTrait;
    
    private SlotNode|null $parentSlot;
    
    /**
     * @param ParentNodeInterface $node
     * @return void
     * @throws EngineException
     */
    public function setParent(ParentNodeInterface $node): void
    {
        $this->parent = $node;
        
        $useSlot = NodeHelper::climbUntil($node, fn(NodeInterface $n) => $n instanceof UseSlotNode);
        $slotName = $useSlot instanceof UseSlotNode ? $useSlot->getSlotName() : ComponentNode::DEFAULT_SLOT;
        $useComponent = NodeHelper::climbUntil($node,
            fn(NodeInterface $n) => $n instanceof ComponentNode || $n instanceof UseComponentNode);
        
        if(!$useComponent instanceof UseComponentNode) {
            $useComponentType = UseComponentNode::getType();
            throw new EngineException(
                "{$this->getType()} node cannot be created outside of a {$useComponentType} node"
            );
        }
        
        $this->parentSlot = $useComponent->getComponent()->getSlot($slotName);
    }
    
    public function render(): string
    {
        return $this->parentSlot?->renderAsIs() ?? '';
    }
}