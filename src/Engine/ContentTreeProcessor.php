<?php


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 */
class ContentTreeProcessor
{
    /**
     * @param ContentTreeNode $rootNode
     * @return string
     * @throws EngineException
     */
    public function processTree(ContentTreeNode $rootNode): string
    {
        if(!$rootNode->isRoot()) {
            throw new EngineException(
                "Expected to process root tag instead of '{$rootNode->getType()}' tag",
                EngineException::INVALID_TAG_STRUCTURE
            );
        }
        
        return $this->processBaseNode($rootNode);
    }
    
    private function processBaseNode(ContentTreeNode $node, array $useSlots = []): string
    {
        $content = "";
        foreach($node->getChildren() as $child) {
            $content .= match ($child->getType()) {
                "content" => $child->getData(),
                "component" => $this->processComponent($child, $useSlots),
                "slot" => match (isset($useSlots[$child->getData()])) {
                    true => $useSlots[$child->getData()](fn() => $this->processBaseNode($child, $useSlots)),
                    false => $this->processBaseNode($child, $useSlots),
                },
                default => $this->throwUnexpectedNodeType($node, $child)
            };
        }
        return $content;
    }
    
    function processComponent(ContentTreeNode $node, array $parentUseSlots = []): string
    {
        $internalNode = null;
        $externalNode = null;
        foreach($node->getChildren() as $child) {
            if($child->getType() === "component_internal") {
                $internalNode = $child;
            } else if($child->getType() === "component_external") {
                $externalNode = $child;
            } else {
                $this->throwUnexpectedNodeType($node, $child);
            }
        }
        
        if(!$internalNode) {
            throw new EngineException(
                "Illegal construction of component tag requires an internal tag",
                EngineException::INVALID_TAG_STRUCTURE
            );
        }
        
        if(!$externalNode) {
            throw new EngineException(
                "Illegal construction of component tag requires an external tag",
                EngineException::INVALID_TAG_STRUCTURE
            );
        }
        
        $implicitDefaultSlotChildren = [];
        $usedSlots = [];
        foreach($externalNode->getChildren() as $externalChild) {
            if($externalChild->getType() === "use_slot") {
                $slotName = $externalChild->getData();
                $usedSlots[$slotName] = fn(callable $parentSlot) => $this->useSlot($externalChild->getChildren(), $parentSlot, $parentUseSlots);
            } else {
                $implicitDefaultSlotChildren[] = $externalChild;
            }
        }
        
        if(!isset($usedSlots["default"])) {
            $usedSlots["default"] = fn(callable $parentSlot) => $this->useSlot($implicitDefaultSlotChildren, $parentSlot, $parentUseSlots);
        }
        
        $doInnerNode = function(ContentTreeNode $node, array $useSlots) use (&$doInnerNode): string {
            $content = "";
            foreach($node->getChildren() as $child) {
                $content .= match ($child->getType()) {
                    "content" => $child->getData(),
                    "component" => $this->processComponent($child, $useSlots),
                    "slot" => match (isset($useSlots[$child->getData()])) {
                        true => $useSlots[$child->getData()](fn() => $doInnerNode($child, $useSlots)),
                        false => $doInnerNode($child, $useSlots),
                    },
                    default => $this->throwUnexpectedNodeType($node, $child)
                };
            }
            return $content;
        };
        
        return $this->processBaseNode($internalNode, $usedSlots);
    }
    
    private function useSlot(array $nodes, callable $parentSlot, array $useSlots): string
    {
        $content = "";
        /** @var ContentTreeNode $child */
        foreach($nodes as $child) {
            $content .= match ($child->getType()) {
                "content" => $child->getData(),
                "component" => $this->processComponent($child, $useSlots),
                "parent_slot" => $parentSlot(),
                "slot" => match (isset($useSlots[$child->getData()])) {
                    true => $useSlots[$child->getData()](fn() => $this->useSlot($child->getChildren(), $parentSlot, $useSlots)),
                    false => $this->useSlot($child->getChildren(), $parentSlot, $useSlots),
                },
                default => $this->throwUnexpectedNodeType($child->getParent(), $child)
            };
        }
        return $content;
    }
    
    private function throwUnexpectedNodeType(ContentTreeNode $parent, ContentTreeNode|string $child): never
    {
        if(is_string($child)) {
            throw new EngineException(
                "Unexpected content written directly in '{$parent->getType()}' tag",
                EngineException::INVALID_TAG_STRUCTURE
            );
        }
        
        throw new EngineException(
            "Unexpected tag '{$child->getType()}' in '{$parent->getType()}' tag",
            EngineException::INVALID_TAG_STRUCTURE
        );
    }
}