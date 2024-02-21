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

trait ChildNodeTrait
{
    private ParentNodeInterface $parent;
    
    public function setParent(ParentNodeInterface $node): void
    {
        $this->parent = $node;
    }
    
    public function getParent(): ParentNodeInterface
    {
        return $this->parent;
    }
}