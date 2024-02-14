<?php


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 */
class OutputBuffer
{
    private readonly string $id;
    private string|null $output;
    private int|null $level;
    protected array $tags;
    
    public function __construct(
        private readonly string $name,
    )
    {
        $this->output = null;
        $this->level = null;
        $this->id = uniqid("{$this->name}:");
        $this->tags = [];
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @return $this
     * @throws EngineException
     */
    public function open(): static
    {
        if(!$this->wasOpened()) {
            ob_start(function(string $buffer) {
                $this->output = $buffer;
                return "";
            });
            $this->level = ob_get_level();
        }
        
        return $this;
    }
    
    public function wasOpened(): bool
    {
        return $this->level !== null;
    }
    
    public function isOpen(): bool
    {
        return $this->wasOpened() && !$this->isClosed();
    }
    
    private function isCurrentOutputBuffer(): bool
    {
        return $this->level === ob_get_level();
    }
    
    /**
     * @return $this
     * @throws EngineException
     */
    public function close(): static
    {
        if($this->isOpen()) {
            if(!$this->isCurrentOutputBuffer()) {
                throw new EngineException(
                    "The output buffer '{$this->name}' cannot be closed before other higher output buffer.",
                    EngineException::OB_INVALID_STATE
                );
            }
            
            ob_end_clean();
        }
        
        return $this;
    }
    
    /**
     * @return $this
     * @throws EngineException
     */
    public function forceClose(): static
    {
        while($this->level !== null && ob_get_level() >= $this->level && ob_get_level() > 0) {
            if(ob_end_clean() === false && ob_end_flush() === false) {
                throw new EngineException(
                    "Failed to forcefully close output buffer '{$this->name}' because an other higher output buffer prevents closing.",
                    EngineException::OB_INVALID_STATE
                );
            }
        }
        return $this;
    }
    
    public function isClosed(): bool
    {
        return $this->output !== null;
    }
    
    /**
     * @return string
     * @throws EngineException
     */
    public function getOutput(): string
    {
        if(!$this->isClosed()) {
            throw new EngineException(
                "Cannot get output from output buffer '{$this->name}' since it was never closed.",
                EngineException::OB_INVALID_STATE
            );
        }
        
        return $this->output;
    }
    
    /**
     * @param string|callable $content
     * @return $this
     * @throws EngineException
     */
    public function writeContent(string|callable $content): static
    {
        if(!$this->isOpen()) {
            throw new EngineException(
                "Cannot write content into closed output buffer '{$this->name}'.",
                EngineException::OB_INVALID_STATE
            );
        }
        
        if(!$this->isCurrentOutputBuffer()) {
            throw new EngineException(
                "Cannot write content into output buffer '{$this->name}' when other higher output buffer is still open.",
                EngineException::OB_INVALID_STATE
            );
        }
        
        if(is_string($content)) {
            echo $content;
        } else {
            $content($this);
        }
        
        return $this;
    }
    
    public function addTag(string $tag, string $value = ''): static
    {
        if(!$this->hasTag($tag)) {
            $this->tags[$tag] = $value;
        }
        
        return $this;
    }
    
    public function clearTag(string $tag): static
    {
        unset($this->tags[$tag]);
        
        return $this;
    }
    
    public function hasTag(string $tag): bool
    {
        return array_key_exists($tag, $this->tags);
    }
    
    public function getTag(string $name): string|null
    {
        return $this->tags[$name] ?? null;
    }
}