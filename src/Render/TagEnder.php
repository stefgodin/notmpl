<?php


namespace Stefmachine\NoTmpl\Render;

class TagEnder
{
    /** @var callable */
    private mixed $end;
    
    public function __construct(callable $end)
    {
        $this->end = $end;
    }
    
    public function end(): void
    {
        ($this->end)();
    }
}