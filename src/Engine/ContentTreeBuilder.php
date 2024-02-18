<?php


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 */
class ContentTreeBuilder
{
    private int|null $level;
    private bool $stopping;
    private ContentTreeNode $currentNode;
    
    public function __construct(string $name)
    {
        $this->level = null;
        $this->stopping = false;
        $this->currentNode = new ContentTreeNode("root", $name);
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
                    $this->currentNode->addChildNode("content", $buffer);
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
    
    public function isOpen(): bool
    {
        return $this->level !== null && ob_get_level() >= $this->level;
    }
    
    /**
     * @return ContentTreeNode
     * @throws EngineException
     */
    public function buildContentTree(): ContentTreeNode
    {
        if(!$this->currentNode->isRoot()) {
            throw new EngineException(
                "Tag '{$this->currentNode->getType()}' was not closed",
                EngineException::INVALID_TAG_STRUCTURE
            );
        }
        
        return $this->currentNode;
    }
    
    /**
     * @param string $type
     * @param string $name
     * @return $this
     * @throws EngineException
     */
    public function openTag(string $type, string $name): static
    {
        $wasOpened = $this->isOpen();
        if($wasOpened) {
            $this->stopCapture();
        }
        
        $this->currentNode = $this->currentNode->addChildNode($type, $name);
        
        if($wasOpened) {
            $this->startCapture();
        }
        
        return $this;
    }
    
    /**
     * @param string $type
     * @return $this
     * @throws EngineException
     */
    public function closeTag(string $type): static
    {
        $wasOpened = $this->isOpen();
        if($wasOpened) {
            $this->stopCapture();
        }
        
        if($this->currentNode->getType() !== $type) {
            throw new EngineException(
                "Cannot end '{$this->currentNode->getType()}' tag with '{$type}'",
                EngineException::INVALID_TAG_STRUCTURE
            );
        }
        
        $this->currentNode = $this->currentNode->getParent();
        
        if($wasOpened) {
            $this->startCapture();
        }
        
        return $this;
    }
}