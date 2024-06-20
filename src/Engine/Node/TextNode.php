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

class TextNode implements NodeInterface, ChildNodeInterface, ParentNodeInterface
{
    use TypeTrait;
    use ChildNodeTrait;
    use ParentNodeTrait {
        render as renderChildren;
    }
    
    public function __construct(
        private mixed $value = null,
    ) {}
    
    public function render(): string
    {
        return self::escHtml($this->value) . self::escHtml($this->renderChildren());
    }
    
    private static function escHtml(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE);
    }
}