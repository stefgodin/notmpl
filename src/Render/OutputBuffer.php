<?php


namespace StefGodin\NoTmpl\Render;

use StefGodin\NoTmpl\Common\TaggableTrait;
use StefGodin\NoTmpl\Exception\RenderError;
use StefGodin\NoTmpl\Exception\RenderException;

/**
 * @internal
 */
class OutputBuffer
{
    use TaggableTrait;
    
    /** @var OutputBuffer[] */
    private static array $allBuffers = [];
    private readonly string $id;
    
    private static function getCurrentLevelName(): string
    {
        return (self::$allBuffers[ob_get_level()] ?? null)?->getName() ?? 'unknown';
    }
    
    private string|null $output;
    private int|null $level;
    
    public function __construct(
        private readonly string $name,
    )
    {
        $this->output = null;
        $this->level = null;
        $this->id = uniqid("{$this->name}:");
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
     * @throws RenderException
     */
    public function open(): static
    {
        if($this->isOpen()) {
            throw new RenderException(
                "The output buffer '{$this->name}' is already opened.",
                RenderError::OB_INVALID_STATE
            );
        }
        
        ob_start(function(string $buffer) {
            $this->output = $buffer;
            unset(self::$allBuffers[$this->level]);
            return "";
        });
        $this->level = ob_get_level();
        self::$allBuffers[$this->level] = $this;
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
     * @throws RenderException
     */
    public function close(): static
    {
        if($this->isClosed()) {
            throw new RenderException(
                "The output buffer '{$this->name}' is already closed.",
                RenderError::OB_INVALID_STATE
            );
        }
        
        if(!$this->wasOpened()) {
            throw new RenderException(
                "The output buffer '{$this->name}' cannot be closed because it was never opened in the first place.",
                RenderError::OB_INVALID_STATE
            );
        }
        
        if(!$this->isCurrentOutputBuffer()) {
            $higherContextName = self::getCurrentLevelName();
            throw new RenderException(
                "The output buffer '{$this->name}' cannot be closed before non-closed output buffer '{$higherContextName}'.",
                RenderError::OB_INVALID_STATE
            );
        }
        
        ob_end_clean();
        return $this;
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function forceClose(): static
    {
        while($this->level !== null && ob_get_level() >= $this->level && ob_get_level() > 0) {
            if(ob_end_clean() === false && ob_end_flush() === false) {
                $higherContextName = self::getCurrentLevelName();
                throw new RenderException(
                    "Failed to forcefully close output buffer '{$this->name}' because '{$higherContextName}' context prevents closing.",
                    RenderError::OB_INVALID_STATE
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
     * @throws RenderException
     */
    public function getOutput(): string
    {
        if(!$this->isClosed()) {
            throw new RenderException(
                "Cannot get output from output buffer '{$this->name}' since it was never closed.",
                RenderError::OB_INVALID_STATE
            );
        }
        
        return $this->output;
    }
    
    /**
     * @param string|callable $content
     * @return $this
     * @throws RenderException
     */
    public function writeContent(string|callable $content): static
    {
        if(!$this->isOpen()) {
            throw new RenderException(
                "Cannot write content into closed output buffer '{$this->name}'.",
                RenderError::OB_INVALID_STATE
            );
        }
        
        if(!$this->isCurrentOutputBuffer()) {
            $higherContextName = self::getCurrentLevelName();
            throw new RenderException(
                "Cannot write content into output buffer '{$this->name}' when other higher context '{$higherContextName}' is still open.",
                RenderError::OB_INVALID_STATE
            );
        }
        
        if(is_string($content)) {
            echo $content;
        } else {
            $content($this);
        }
        
        return $this;
    }
}