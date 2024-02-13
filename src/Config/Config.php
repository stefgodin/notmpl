<?php


namespace StefGodin\NoTmpl\Config;

use StefGodin\NoTmpl\Escape\Esc;
use StefGodin\NoTmpl\Singleton\SingletonTrait;

class Config
{
    use SingletonTrait;
    
    private array $renderGlobalParams;
    private array $templateDirectories;
    private array $templateAliases;
    private string|null $escaperEncoding;
    
    public function __construct()
    {
        $this->renderGlobalParams = [];
        $this->templateDirectories = [];
        $this->templateAliases = [];
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
            $this->templateDirectories[] = rtrim($directory, '/\\');
        }
        
        return $this;
    }
    
    public function addTemplateDirectories(array $directories): static
    {
        foreach($directories as $directory) {
            $this->addTemplateDirectory($directory);
        }
        
        return $this;
    }
    
    public function setTemplateDirectories(array $directories): static
    {
        $this->templateDirectories = [];
        $this->addTemplateDirectories($directories);
        return $this;
    }
    
    public function getTemplateDirectories(): array
    {
        return $this->templateDirectories;
    }
    
    public function setTemplateAlias(string $template, string $alias): static
    {
        $this->templateAliases[$alias] = $template;
        return $this;
    }
    
    public function setTemplateAliases(array $templateAliases): static
    {
        $this->templateAliases = [];
        foreach($templateAliases as $alias => $template) {
            $this->setTemplateAlias($template, $alias);
        }
        return $this;
    }
    
    public function getTemplateAliases(): array
    {
        return $this->templateAliases;
    }
    
    public function setEscaperEncoding(string|null $_encoding): static
    {
        $this->escaperEncoding = $_encoding;
        Esc::resetInstance();
        return $this;
    }
    
    public function getEscaperEncoding(): string|null
    {
        return $this->escaperEncoding;
    }
}