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

use Iterator;

class NodeBuilderIterator implements Iterator, EnderInterface
{
    private int $cursor;
    private mixed $currentValue;
    private bool $opened;
    
    /** @var callable */
    private readonly mixed $open;
    /** @var callable */
    private readonly mixed $close;
    
    public function __construct(
        callable             $open,
        callable             $close,
        private readonly int $startIndex,
        private readonly int $endIndex,
    )
    {
        $this->open = $open;
        $this->close = $close;
        $this->cursor = $this->startIndex;
        $this->currentValue = null;
        $this->opened = false;
    }
    
    public function current(): mixed
    {
        return $this->currentValue;
    }
    
    public function next(): void
    {
        $this->cursor++;
        $this->startNewNode();
    }
    
    public function key(): mixed
    {
        return $this->cursor;
    }
    
    public function valid(): bool
    {
        return $this->cursor >= $this->startIndex && $this->cursor <= $this->endIndex;
    }
    
    public function rewind(): void
    {
        $this->cursor = $this->startIndex;
        $this->startNewNode();
    }
    
    private function startNewNode(): void
    {
        $this->end();
        
        if($this->valid()) {
            $this->currentValue = ($this->open)();
            $this->opened = true;
        } else {
            $this->currentValue = null;
        }
    }
    
    /**
     * Properly closes the last built node, handy when you want to stop iteration early (using break)
     *
     * @return void
     */
    public function end(): void
    {
        if($this->opened) {
            ($this->close)();
            $this->opened = false;
        }
    }
}