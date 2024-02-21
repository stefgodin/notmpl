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

interface ChildNodeInterface extends NodeInterface
{
    public function setParent(ParentNodeInterface $node): void;
    
    public function getParent(): ParentNodeInterface;
}