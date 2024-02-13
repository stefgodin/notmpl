<?php


namespace StefGodin\NoTmpl\Tests\Escape;

use PHPUnit\Framework\TestCase;
use stdClass;
use StefGodin\NoTmpl\Esc;
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
            self::assertSame('', esc_html($value), "Expected non-stringable '{$type}' to return empty string from " . \StefGodin\NoTmpl\Esc::class . "::html");
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
            self::assertSame($expected, esc_html($value), "Expected stringable '{$name}' to return '{$expected}' string from " . Esc::class . "::html");
        }
    }
}
