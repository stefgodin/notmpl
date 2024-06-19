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

class RenderContext
{
    private NodeTreeBuilder $nodeTreeBuilder;
    
    public function __construct(
        private readonly FileManager $fileManager,
    )
    {
        $this->nodeTreeBuilder = new NodeTreeBuilder();
    }
    
    public function getNodeTreeBuilder(): NodeTreeBuilder
    {
        return $this->nodeTreeBuilder;
    }
    
    public function getFileManager(): FileManager
    {
        return $this->fileManager;
    }
}