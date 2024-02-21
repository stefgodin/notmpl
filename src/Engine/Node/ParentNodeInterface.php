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

interface ParentNodeInterface extends NodeInterface
{
    public function addChild(ChildNodeInterface $node): void;
    
    public function getChildren(): array;
}