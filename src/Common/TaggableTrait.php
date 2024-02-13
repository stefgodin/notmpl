<?php


namespace StefGodin\NoTmpl\Common;

trait TaggableTrait
{
    protected array $tags = [];
    
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