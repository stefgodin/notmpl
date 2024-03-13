<?php
/*
 * This file is part of the NoTMPL package.
 *
 * (c) Stéphane Godin
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */


namespace StefGodin\NoTmpl\Engine;

use StefGodin\NoTmpl\Engine\Node\ChildNodeInterface;
use StefGodin\NoTmpl\Engine\Node\NodeInterface;
use StefGodin\NoTmpl\Engine\Node\ParentNodeInterface;
use StefGodin\NoTmpl\Engine\Node\RawContentNode;
use StefGodin\NoTmpl\Engine\Node\RootNode;
use StefGodin\NoTmpl\Engine\Node\StateListenerInterface;

class NodeTreeBuilder
{
    private int|null $level;
    private bool $stopping;
    private RootNode $rootNode;
    private ParentNodeInterface $currentNode;
    
    public function __construct()
    {
        $this->level = null;
        $this->stopping = false;
        $this->rootNode = new RootNode();
        $this->currentNode = $this->rootNode;
    }
    
    public function getCurrentNode(): NodeInterface
    {
        return $this->currentNode;
    }
    
    /**
     * @return RootNode
     * @throws EngineException
     */
    public function buildTree(): RootNode
    {
        if($this->currentNode !== $this->rootNode) {
            throw new EngineException(
                "{$this->currentNode->getType()} node was left open",
                EngineException::INVALID_TREE_STRUCTURE
            );
        }
        
        return $this->rootNode;
    }
    
    /**
     * @return $this
     * @throws EngineException
     */
    public function startCapture(): static
    {
        if($this->level === null) {
            ob_start(function(string $buffer) {
                if(!$this->stopping) {
                    throw new EngineException(
                        "Attempting to close output buffer outside of content manager",
                        EngineException::ILLEGAL_BUFFER_ACTION
                    );
                }
                
                if(!empty($buffer)) {
                    $this->currentNode->addChild(new RawContentNode($buffer));
                }
                
                return "";
            });
            $this->level = ob_get_level();
        }
        
        return $this;
    }
    
    /**
     * @param bool $force
     * @return $this
     * @throws EngineException
     */
    public function stopCapture(bool $force = false): static
    {
        if($this->level !== null) {
            $this->stopping = true;
            if(!$force && $this->level < ob_get_level()) {
                throw new EngineException(
                    "An output buffer was left open outside of content manager",
                    EngineException::ILLEGAL_BUFFER_ACTION
                );
            }
            
            if(ob_end_clean() === false && ob_end_flush() === false) {
                throw new EngineException(
                    "Failed to stop content manager capture",
                    EngineException::ILLEGAL_BUFFER_ACTION
                );
            }
            $this->stopping = true;
            $this->level = null;
        }
        
        return $this;
    }
    
    /**
     * @param callable $call
     * @return $this
     * @throws EngineException
     */
    public function capture(callable $call): static
    {
        $wasClosed = !$this->isOpen();
        if($wasClosed) {
            $this->startCapture();
        }
        
        $call();
        
        if($wasClosed) {
            $this->stopCapture();
        }
        
        return $this;
    }
    
    /**
     * @param ChildNodeInterface $node
     * @return $this
     * @throws EngineException
     */
    public function addNode(ChildNodeInterface $node): static
    {
        $wasOpened = $this->isOpen();
        if($wasOpened) {
            $this->stopCapture();
        }
        
        $this->currentNode->addChild($node);
        $node->setParent($this->currentNode);
        if($node instanceof ParentNodeInterface) {
            $this->currentNode = $node;
            if($node instanceof StateListenerInterface) {
                $node->onOpen();
            }
        }
        
        if($wasOpened) {
            $this->startCapture();
        }
        
        return $this;
    }
    
    /**
     * @param class-string<NodeInterface>|NodeInterface $expect
     * @return $this
     * @throws EngineException
     */
    public function exitNode(string|NodeInterface $expect): static
    {
        if((is_string($expect) && $this->currentNode::getType() !== $expect)
            || ($expect instanceof NodeInterface && $this->currentNode !== $expect)) {
            $type = is_string($expect) ? $expect : $expect::getType();
            throw new EngineException(
                "Cannot end {$type} node, {$this->currentNode->getType()} node was left open",
                EngineException::INVALID_TREE_STRUCTURE
            );
        }
        
        if($this->currentNode instanceof ChildNodeInterface) {
            $wasOpened = $this->isOpen();
            if($wasOpened) {
                $this->stopCapture();
            }
            
            if($this->currentNode instanceof StateListenerInterface) {
                $this->currentNode->onClose();
            }
            $this->currentNode = $this->currentNode->getParent();
            
            if($wasOpened) {
                $this->startCapture();
            }
        }
        
        return $this;
    }
    
    private function isOpen(): bool
    {
        return $this->level !== null && ob_get_level() >= $this->level;
    }
}