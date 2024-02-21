<?php
/*
 * This file is part of the NoTMPL package.
 *
 * (c) Stéphane Godin
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 * @private
 */
class ScopeManager
{
    private array $definedNs;
    private array $usedNs;
    private array $previousBindings = [];
    private array $bindingRefs = [];
    
    public function __construct()
    {
        $this->definedNs = [];
    }
    
    public function startNamespace(): void
    {
        $this->definedNs[] = [];
    }
    
    public function useNamespace(): void
    {
        if(empty($this->definedNs)) {
            return;
        }
        
        $index = array_key_last($this->definedNs);
        $this->usedNs[] = $this->definedNs[$index];
        unset($this->definedNs[$index]);
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
        if(empty($this->definedNs)) {
            return;
        }
        
        $this->definedNs[array_key_last($this->definedNs)][$name] = $bindings;
    }
    
    public function useScope(string $name, mixed &$bindings): void
    {
        $newBindings = [];
        if(!empty($this->usedNs)) {
            $namespaceIndex = array_key_last($this->usedNs);
            $newBindings = $this->usedNs[$namespaceIndex][$name] ?? [];
        }
        
        $this->previousBindings[] = $bindings;
        $this->bindingRefs[] = &$bindings;
        $bindings = $newBindings;
    }
    
    public function leaveScope(): void
    {
        if(empty($this->previousBindings)) {
            return;
        }
        
        $index = array_key_last($this->previousBindings);
        $this->bindingRefs[$index] = $this->previousBindings[$index];
        unset($this->previousBindings[$index]);
        unset($this->bindingRefs[$index]);
    }
}