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

class UseSlotNode implements NodeInterface, ParentNodeInterface, ChildNodeInterface
{
    use ChildNodeTrait;
    use ParentNodeTrait;
    use TypeTrait;
    
    public function __construct(
        private readonly string $slotName = ComponentNode::DEFAULT_SLOT,
    ) {}
    
    public function setParent(ParentNodeInterface $node): void
    {
        if(!$node instanceof UseComponentNode) {
            $useComponentType = UseComponentNode::getType();
            throw new EngineException(
                "{$this->getType()} node can only be added to a {$useComponentType} node under use",
                EngineException::INVALID_TREE_STRUCTURE
            );
        }
        
        $this->parent = $node;
    }
    
    public function getSlotName(): string
    {
        return $this->slotName;
    }
}