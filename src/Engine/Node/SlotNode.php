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
    
    private NodeInterface|null $replacementNode;
    
    public function __construct(
        private readonly string $name,
        private readonly array  $bindings = [],
    )
    {
        $this->replacementNode = null;
    }
    
    public function setParent(ParentNodeInterface $node): void
    {
        NodeHelper::climbToFirst($node, ComponentNode::class)?->addSlot($this);
        $this->parent = $node;
    }
    
    public function setReplacementNode(NodeInterface $replacementNode): void
    {
        $this->replacementNode = $replacementNode;
    }
    
    public function isReplaced(): bool
    {
        return $this->replacementNode !== null;
    }
    
    public function render(): string
    {
        return $this->replacementNode?->render() ?? $this->renderAsIs();
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