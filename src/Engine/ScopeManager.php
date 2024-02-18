<?php


namespace StefGodin\NoTmpl\Engine;

class ScopeManager
{
    private array $ns;
    private array $usedNs;
    private array $previousBindingsStack = [];
    private array $bindingsRefStack = [];
    
    public function __construct()
    {
        $this->ns = [];
    }
    
    public function startNamespace(): void
    {
        $this->ns[] = [];
    }
    
    public function useNamespace(): void
    {
        if(empty($this->ns)) {
            return;
        }
        
        $index = array_key_last($this->ns);
        $this->usedNs[] = $this->ns[$index];
        unset($this->ns[$index]);
    }
    
    public function endNamespace(): void
    {
        if(empty($this->usedNs)) {
            return;
        }
        
        unset($this->usedNs[array_key_last($this->usedNs)]);
    }
    
    public function defineScope(string $name, array $bindings): void
    {
        if(empty($this->ns)) {
            $this->startNamespace();
        }
        
        $this->ns[array_key_last($this->ns)][$name] = $bindings;
    }
    
    public function endScopeDefine(): void
    {
        if(empty($this->ns)) {
            return;
        }
        
        $namespaceIndex = array_key_last($this->ns);
        
        if(empty($this->ns[$namespaceIndex])) {
            return;
        }
        
        unset($this->ns[$namespaceIndex][array_key_last($this->ns[$namespaceIndex])]);
    }
    
    public function useScope(string $name, mixed &$bindings): void
    {
        $newBindings = [];
        if(!empty($this->usedNs)) {
            $namespaceIndex = array_key_last($this->usedNs);
            $newBindings = $this->usedNs[$namespaceIndex][$name] ?? [];
        }
        
        $this->previousBindingsStack[] = $bindings;
        $this->bindingsRefStack[] = &$bindings;
        $bindings = $newBindings;
    }
    
    public function resetUseScope(): void
    {
        if(empty($this->previousBindingsStack)) {
            return;
        }
        
        $index = array_key_last($this->previousBindingsStack);
        $this->bindingsRefStack[$index] = $this->previousBindingsStack[$index];
        unset($this->previousBindingsStack[$index]);
        unset($this->bindingsRefStack[$index]);
    }
}