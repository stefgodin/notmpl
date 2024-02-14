<?php


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 */
class OutputBufferList
{
    /** @var OutputBuffer[] */
    private array $outputBuffers;
    
    public function __construct()
    {
        $this->outputBuffers = [];
    }
    
    public function add(string $name): OutputBuffer
    {
        return $this->outputBuffers[] = new OutputBuffer($name);
    }
    
    public function getFirst(callable ...$filters): OutputBuffer|null
    {
        for($i = 0; $i < count($this->outputBuffers); $i++) {
            $found = true;
            foreach($filters as $filter) {
                $found = $found && $filter($this->outputBuffers[$i]);
            }
            
            if($found) {
                return $this->outputBuffers[$i];
            }
        }
        
        return null;
    }
    
    public function getLast(callable ...$filters): OutputBuffer|null
    {
        for($i = count($this->outputBuffers) - 1; $i >= 0; $i--) {
            $found = true;
            foreach($filters as $filter) {
                $found = $found && $filter($this->outputBuffers[$i]);
            }
            
            if($found) {
                return $this->outputBuffers[$i];
            }
        }
        
        return null;
    }
    
    /**
     * @param callable ...$filters
     * @return OutputBuffer[]
     */
    public function all(callable ...$filters): array
    {
        return array_values(array_filter($this->outputBuffers, function(OutputBuffer $ob) use (&$filters) {
            foreach($filters as $filter) {
                if(!$filter($ob)) {
                    return false;
                }
            }
            
            return true;
        }));
    }
    
    public static function isOpen(): callable
    {
        return fn(OutputBuffer $ob) => $ob->isOpen();
    }
    
    public static function isClosed(): callable
    {
        return fn(OutputBuffer $ob) => $ob->isClosed();
    }
    
    public static function hasTag(string $tag, string|null $value = null): callable
    {
        return fn(OutputBuffer $ob) => $ob->hasTag($tag) && ($value === null || $ob->getTag($tag) === $value);
    }
    
    public static function hasName(string $name): callable
    {
        return fn(OutputBuffer $ob) => $ob->getName() === $name;
    }
}