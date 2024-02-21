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

class NodeHelper
{
    public static function climbUntil(NodeInterface $node, callable $climb): NodeInterface|null
    {
        while($node && !$climb($node)) {
            $node = $node instanceof ChildNodeInterface ? $node->getParent() : null;
        }
        
        return $node;
    }
}