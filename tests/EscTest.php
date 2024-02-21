<?php
/*
 * This file is part of the NoTMPL package.
 *
 * (c) Stéphane Godin
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */


namespace StefGodin\NoTmpl\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use function StefGodin\NoTmpl\esc_html;

class EscTest extends TestCase
{
    /** @test */
    public function should_return_empty_string_when_given_non_stringable(): void
    {
        $nonStringable = [
            'null' => null,
            'object' => new stdClass(),
            'function' => function() {},
            'array' => [],
        ];
        
        foreach($nonStringable as $type => $value) {
            self::assertSame('', esc_html($value), "Expected non-stringable '{$type}' to return empty string from esc_html");
        }
    }
    
    /** @test */
    public function should_return_string_when_given_stringable(): void
    {
        $tests = [
            'test as string' => ['test', 'test'],
            'true as bool' => [true, '1'],
            '1 as integer' => [1, '1'],
            'stringable class' => [
                new class {
                    public function __toString(): string
                    {
                        return 'hello';
                    }
                },
                'hello',
            ],
        ];
        
        foreach($tests as $name => [$value, $expected]) {
            self::assertSame($expected, esc_html($value), "Expected stringable '{$name}' to return '{$expected}' string from esc_html");
        }
    }
}
