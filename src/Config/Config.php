<?php


namespace Stefmachine\NoTmpl\Config;

use Stefmachine\NoTmpl\Escape\Escaper;
use Stefmachine\NoTmpl\Singleton\SingletonTrait;

class Config
{
    use SingletonTrait;
    
    protected array $renderGlobalParams;
    protected array $templateDirectories;
    protected string|null $escaperEncoding;
    
    public function __construct()
    {
        $this->renderGlobalParams = [];
        $this->templateDirectories = [];
        $this->escaperEncoding = null;
    }
    
    public function setRenderGlobalParam(string $name, mixed $value): static
    {
        $this->renderGlobalParams[$name] = $value;
        return $this;
    }
    
    public function addRenderGlobalParams(array $values): static
    {
        foreach($values as $key => $value) {
            $this->setRenderGlobalParam($key, $value);
        }
        return $this;
    }
    
    public function setRenderGlobalParams(array $values): static
    {
        $this->renderGlobalParams = $values;
        return $this;
    }
    
    public function getRenderGlobalParams(): array
    {
        return $this->renderGlobalParams;
    }
    
    public function addTemplateDirectory(string $directory): static
    {
        if(!in_array($directory, $this->templateDirectories)) {
            $this->templateDirectories[] = $directory;
        }
        
        return $this;
    }
    
    public function addTemplateDirectories(array $directories): static
    {
        foreach($directories as $directory) {
            $directory = rtrim($directory, '/\\');
            $this->addTemplateDirectory($directory);
        }
        
        return $this;
    }
    
    public function setTemplateDirectories(array $directories): static
    {
        $this->templateDirectories = $directories;
        return $this;
    }
    
    public function getTemplateDirectories(): array
    {
        return $this->templateDirectories;
    }
    
    public function setEscaperEncoding(string|null $_encoding): static
    {
        $this->escaperEncoding = $_encoding;
        Escaper::resetInstance();
        return $this;
    }
    
    public function getEscaperEncoding(): string|null
    {
        return $this->escaperEncoding;
    }
}