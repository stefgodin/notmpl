<?php
/*
 * This file is part of the NoTMPL package.
 *
 * (c) Stéphane Godin
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */


namespace StefGodin\NoTmpl\Engine\Node;

class RawContentNode implements NodeInterface, ChildNodeInterface
{
    use TypeTrait;
    use ChildNodeTrait;
    
    public function __construct(
        private readonly string $content,
    ) {}
    
    public function render(): string
    {
        return $this->content;
    }
}