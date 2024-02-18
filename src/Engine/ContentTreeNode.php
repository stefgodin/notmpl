<?php


namespace StefGodin\NoTmpl\Engine;

class ContentTreeNode
{
    protected ContentTreeNode $parent;
    
    /** @var ContentTreeNode[] */
    private array $children;
    
    public function __construct(
        private readonly string $type,
        private readonly string $data,
    ) {}
    
    public function addChildNode(string $type, string $data): ContentTreeNode
    {
        $child = new ContentTreeNode($type, $data);
        $this->children[] = $child;
        $child->parent = $this;
        return $child;
    }
    
    public function getData(): string
    {
        return $this->data;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function getChildren(): array
    {
        return $this->children;
    }
    
    public function getParent(): ContentTreeNode
    {
        return $this->parent ?? $this;
    }
    
    public function isRoot(): bool
    {
        return !isset($this->parent);
    }
}