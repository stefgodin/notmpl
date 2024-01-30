<?php


namespace Stefmachine\NoTmpl\Render;

class RenderBlockMap
{
    /** @var RenderBlock[] */
    private array $blocks;
    /** @var RenderBlock */
    private array $replaceMap;
    
    public function __construct()
    {
        $this->blocks = [];
    }
    
}