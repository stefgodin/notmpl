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

/**
 * @property UseComponentNode $parent
 */
class UseSlotNode implements NodeInterface, ParentNodeInterface, ChildNodeInterface, StateListenerInterface
{
    use ChildNodeTrait;
    use ParentNodeTrait;
    use TypeTrait;
    
    private mixed $oldBindings;
    
    public function __construct(
        private readonly string $slotName = ComponentNode::DEFAULT_SLOT,
        private mixed           &$bindingsRef = null,
    )
    {
        $this->oldBindings = null;
    }
    
    /**
     * @param ParentNodeInterface $node
     * @return void
     * @throws EngineException
     */
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
    
    public function onOpen(): void
    {
        $slotBindings = $this->parent->getComponent()->getSlot(
            $this->slotName,
            $this->parent->getUseSlotIndex($this),
        )?->getBindings() ?? null;
        
        $this->oldBindings = $this->bindingsRef;
        $this->bindingsRef = $slotBindings;
    }
    
    public function onClose(): void
    {
        $this->bindingsRef = $this->oldBindings;
        $this->oldBindings = null;
    }
}