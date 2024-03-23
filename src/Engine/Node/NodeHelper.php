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
    /**
     * @template T
     * @param NodeInterface $node
     * @param class-string<T> ...$inTypes
     * @return T|null
     */
    public static function climbToFirst(NodeInterface $node, string ...$inTypes): NodeInterface|null
    {
        if(empty($inTypes)) {
            return null;
        }
        
        while($node && empty(array_filter($inTypes, fn(string $type) => $node instanceof $type))) {
            $node = $node instanceof ChildNodeInterface ? $node->getParent() : null;
        }
        
        return $node;
    }
}