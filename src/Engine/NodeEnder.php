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

class NodeEnder implements EnderInterface
{
    /** @var callable */
    private readonly mixed $end;
    
    public function __construct(callable $end)
    {
        $this->end = $end;
    }
    
    public function end(): void
    {
        ($this->end)();
    }
}