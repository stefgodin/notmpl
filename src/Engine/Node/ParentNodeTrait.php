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

trait ParentNodeTrait
{
    /** @var ChildNodeInterface[] */
    private array $children = [];
    
    public function addChild(ChildNodeInterface $node): void
    {
        $this->children[] = $node;
    }
    
    public function getChildren(): array
    {
        return $this->children;
    }
    
    public function render(): string
    {
        $content = "";
        foreach($this->getChildren() as $child) {
            $content .= $child->render();
        }
        return $content;
    }
}