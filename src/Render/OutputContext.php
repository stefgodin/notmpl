<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderException;

/**
 * @internal
 */
class OutputContext
{
    private string|null $output;
    private int|null $level;
    
    public function __construct(
        private string $name,
    )
    {
        $this->output = null;
        $this->level = null;
    }
    
    public function setName(string $_name): static
    {
        $this->name = $_name;
        return $this;
    }
    
    public static function create(string $name): OutputContext
    {
        return new OutputContext($name);
    }
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function open(): static
    {
        if($this->isOpen()) {
            throw new RenderException("The output context '{$this->name}' is already opened.");
        }
        
        ob_start(function(string $buffer) {
            $this->output = $buffer;
            return "";
        });
        $this->level = ob_get_level();
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
    
    /**
     * @return $this
     * @throws RenderException
     */
    public function close(): static
    {
        if($this->isClosed()) {
            throw new RenderException("The output context '{$this->name}' is already closed.");
        }
        
        if(!$this->wasOpened()) {
            throw new RenderException("The output context '{$this->name}' cannot be closed because it was never opened in the first place.");
        }
        
        if($this->level !== ob_get_level()) {
            throw new RenderException("The output context '{$this->name}' precedes other non-closed output contexts/buffers.");
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
        if(!$this->isOpen()) {
            throw new RenderException("Cannot force to close output context '{$this->name}' since it was not opened.");
        }
        
        $oldLevel = null;
        while(ob_get_level() >= $this->level && ob_get_level() > 0 && $oldLevel !== ob_get_level()) {
            $oldLevel = ob_get_level();
            ob_end_clean();
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
            throw new RenderException("Cannot get output from output context '{$this->name}' since it was never closed.");
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
            throw new RenderException("Cannot write content into closed output context '{$this->name}'.");
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
            throw new RenderException("Cannot include file into closed output context '{$this->name}'.");
        }
        
        if(!file_exists($file)) {
            throw new RenderException("File '{$file}' not found for rendering.");
        }
        
        if(pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            IsolatedPhpRenderer::render($file, $vars);
        } else {
            $this->writeContent(file_get_contents($file));
        }
        
        return $this;
    }
}