<?php


namespace StefGodin\NoTmpl;

use StefGodin\NoTmpl\Engine\EngineException;
use StefGodin\NoTmpl\Engine\RenderContext;
use StefGodin\NoTmpl\Engine\RenderContextStack;
use StefGodin\NoTmpl\Engine\TemplateResolver;
use Throwable;

/**
 * Static object class to regroup all rendering functions of the NoTmpl library
 */
class NoTmpl
{
    private array $renderGlobalParams;
    private array $templateDirectories;
    private array $templateAliases;
    
    public function __construct()
    {
        $this->renderGlobalParams = [];
        $this->templateDirectories = [];
        $this->templateAliases = [];
    }
    
    /**
     * Renders a template content with given parameters as variables and returns the resulting rendered content as a string.
     *
     * @param string $file - The file to render, can be a component alias
     * @param array $parameters - The parameters to be passed to the template
     * @return string
     * @throws EngineException
     */
    public function render(string $file, array $parameters = []): string
    {
        $templateResolver = new TemplateResolver(
            $this->templateDirectories,
            $this->templateAliases,
        );
        
        $renderContext = new RenderContext(
            $templateResolver,
            $this->renderGlobalParams
        );
        RenderContextStack::$stack[] = $renderContext;
        try {
            $result = $renderContext->render($file, $parameters);
        } catch(Throwable $e) {
            array_pop(RenderContextStack::$stack);
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $e;
        }
        
        array_pop(RenderContextStack::$stack);
        return $result;
    }
    
    public function setRenderGlobalParam(string $name, mixed $value): static
    {
        $this->renderGlobalParams[$name] = $value;
        return $this;
    }
    
    public function setRenderGlobalParams(array $values): static
    {
        $this->renderGlobalParams = $values;
        return $this;
    }
    
    public function addDirectory(string $directory): static
    {
        if(!in_array($directory, $this->templateDirectories)) {
            $this->templateDirectories[] = rtrim($directory, '/\\');
        }
        
        return $this;
    }
    
    public function addDirectories(array $directories): static
    {
        foreach($directories as $directory) {
            $this->addDirectory($directory);
        }
        
        return $this;
    }
    
    public function setDirectories(array $directories): static
    {
        $this->templateDirectories = [];
        $this->addDirectories($directories);
        return $this;
    }
    
    public function setAlias(string $file, string $alias): static
    {
        $this->templateAliases[$alias] = $file;
        return $this;
    }
    
    public function setAliases(array $templateAliases): static
    {
        $this->templateAliases = [];
        foreach($templateAliases as $alias => $template) {
            $this->setAlias($template, $alias);
        }
        return $this;
    }
}