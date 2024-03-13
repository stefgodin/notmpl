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

class SlotNode implements NodeInterface, ChildNodeInterface, ParentNodeInterface
{
    use ChildNodeTrait;
    use ParentNodeTrait {
        render as renderAsIs;
    }
    use TypeTrait;
    
    private ComponentNode|null $component;
    
    public function __construct(
        private readonly string $name = ComponentNode::DEFAULT_SLOT,
        private readonly array  $bindings = [],
    ) {}
    
    public function setParent(ParentNodeInterface $node): void
    {
        $this->component = NodeHelper::climbUntil($node, fn(NodeInterface $n) => $n instanceof ComponentNode);
        $this->component?->addSlot($this);
        
        $this->parent = $node;
    }
    
    public function getComponent(): ?ComponentNode
    {
        return $this->component;
    }
    
    public function render(): string
    {
        return $this->component?->getUseSlot($this)?->render() ?? $this->renderAsIs();
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getBindings(): array
    {
        return $this->bindings;
    }
}