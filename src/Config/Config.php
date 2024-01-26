<?php


namespace Stefmachine\NoTmpl\Config;

use Stefmachine\NoTmpl\Escape\Escaper;
use Stefmachine\NoTmpl\Singleton\SingletonTrait;

class Config
{
    use SingletonTrait;
    
    protected array $renderGlobalParams;
    protected string|null $escaperEncoding;
    
    public function __construct()
    {
        $this->renderGlobalParams = [];
        $this->escaperEncoding = null;
    }
    
    public function setRenderGlobalParam(string $name, mixed $value): static
    {
        $this->renderGlobalParams[$name] = $value;
        return $this;
    }
    
    public function setRenderGlobalParams(array $values): static
    {
        foreach($values as $key => $value) {
            $this->setRenderGlobalParam($key, $value);
        }
        return $this;
    }
    
    public function getRenderGlobalParams(): array
    {
        return $this->renderGlobalParams;
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