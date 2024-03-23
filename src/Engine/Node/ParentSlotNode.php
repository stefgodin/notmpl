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
    
    private UseSlotNode|null $useSlot;
    
    /**
     * @param ParentNodeInterface $node
     * @return void
     * @throws EngineException
     */
    public function setParent(ParentNodeInterface $node): void
    {
        $this->parent = $node;
        
        $refNode = NodeHelper::climbToFirst($node, UseSlotNode::class, UseComponentNode::class, ComponentNode::class);
        
        if($refNode instanceof ComponentNode) {
            $useComponentType = UseComponentNode::getType();
            EngineException::throwInvalidTreeStructure("{$this::getType()} node must be created within a {$useComponentType} node context");
        }
        
        $this->useSlot = $refNode instanceof UseComponentNode ? $refNode->getImplicitUseSlot() : $refNode;
    }
    
    public function render(): string
    {
        return $this->useSlot->getSlot()?->renderAsIs() ?? '';
    }
}