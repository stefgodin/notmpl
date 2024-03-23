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
    
    private SlotNode|null $slot;
    private mixed $oldBindings;
    
    public function __construct(
        private readonly string $slotName,
        private mixed           &$bindingsRef = null,
    )
    {
        $this->slot = null;
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
            EngineException::throwInvalidTreeStructure("{$this::getType()} node can only be added to a {$useComponentType} node under use");
        }
        
        $this->parent = $node;
    }
    
    public function setSlot(SlotNode $slot): void
    {
        $this->slot = $slot;
    }
    
    public function getSlot(): SlotNode|null
    {
        return $this->slot;
    }
    
    public function getSlotName(): string
    {
        return $this->slotName;
    }
    
    public function onOpen(): void
    {
        $this->oldBindings = $this->bindingsRef;
        $this->bindingsRef = $this->slot?->getBindings() ?? null;
    }
    
    public function onClose(): void
    {
        $this->bindingsRef = $this->oldBindings;
        $this->oldBindings = null;
    }
}