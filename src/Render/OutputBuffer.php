<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderError;
use Stefmachine\NoTmpl\Exception\RenderException;

/**
 * @internal
 */
class OutputBuffer
{
    /** @var OutputBuffer[] */
    private static array $allBuffers = [];
    
    private static function getCurrentLevelName(): string
    {
        return (self::$allBuffers[ob_get_level()] ?? null)?->getName() ?? 'unknown';
    }
    
    private string|null $output;
    private int|null $level;
    
    public function __construct(
        private readonly OutputBufferStack $stack,
        private readonly string            $name,
    )
    {
        $this->output = null;
        $this->level = null;
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
                RenderError::OB_ALREADY_OPENED
            );
        }
        
        ob_start(function(string $buffer) {
            $this->output = $buffer;
            $this->stack->pop();
            unset(self::$allBuffers[$this->level]);
            return "";
        });
        $this->level = ob_get_level();
        $this->stack->push($this);
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
                RenderError::OB_ALREADY_CLOSED
            );
        }
        
        if(!$this->wasOpened()) {
            throw new RenderException(
                "The output buffer '{$this->name}' cannot be closed because it was never opened in the first place.",
                RenderError::OB_NEVER_OPENED
            );
        }
        
        if(!$this->isCurrentOutputBuffer()) {
            $higherContextName = self::getCurrentLevelName();
            throw new RenderException(
                "The output buffer '{$this->name}' cannot be closed before non-closed output buffer '{$higherContextName}'.",
                RenderError::OB_CLOSE_WRONG_DEPTH
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
                    "Failing to forcefully close output buffer '{$this->name}' because '{$higherContextName}' context prevents closing.",
                    RenderError::OB_FORCEFUL_CLOSE_FAILED
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
                RenderError::OB_NEVER_CLOSED
            );
        }
        
        return $this->output;
    }
    
    /**
     * @param string $content
     * @return $this
     * @throws RenderException
     */
    public function writeContent(string $content): static
    {
        if(!$this->isOpen()) {
            throw new RenderException(
                "Cannot write content into closed output buffer '{$this->name}'.",
                RenderError::OB_WRITE_CLOSED
            );
        }
        
        if(!$this->isCurrentOutputBuffer()) {
            $higherContextName = self::getCurrentLevelName();
            throw new RenderException(
                "Cannot write content into output buffer '{$this->name}' when other higher context '{$higherContextName}' is still open.",
                RenderError::OB_WRITE_WRONG_DEPTH
            );
        }
        
        echo $content;
        return $this;
    }
    
    /**
     * @param string $file
     * @param array $vars
     * @return $this
     * @throws RenderException
     */
    public function includeFile(string $file, array $vars = []): static
    {
        if(!$this->isOpen()) {
            throw new RenderException(
                "Cannot include file into closed output buffer '{$this->name}'.",
                RenderError::OB_FILE_INCLUDE_CLOSED
            );
        }
        
        if(!$this->isCurrentOutputBuffer()) {
            $higherContextName = self::getCurrentLevelName();
            throw new RenderException(
                "Cannot include file into output buffer '{$this->name}' when other higher context '{$higherContextName}' is still open.",
                RenderError::OB_FILE_INCLUDE_WRONG_DEPTH
            );
        }
        
        if(!file_exists($file)) {
            throw new RenderException(
                "File '{$file}' not found for rendering.",
                RenderError::OB_FILE_INCLUDE_NOT_FOUND
            );
        }
        
        if(pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            IsolatedPhpRenderer::render($file, $vars);
        } else {
            $this->writeContent(file_get_contents($file));
        }
        
        return $this;
    }
}