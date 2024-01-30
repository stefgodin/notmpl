<?php


namespace Stefmachine\NoTmpl\Tests\Escape;

use PHPUnit\Framework\TestCase;
use stdClass;
use Stefmachine\NoTmpl\Escape\Esc;

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
            self::assertSame('', Esc::html($value), "Expected non-stringable '{$type}' to return empty string from " . Esc::class . "::html");
            self::assertSame('', Esc::htmlAttr($value), "Expected non-stringable '{$type}' to return empty string from " . Esc::class . "::htmlAttr");
            self::assertSame('', Esc::js($value), "Expected non-stringable '{$type}' to return empty string from " . Esc::class . "::js");
            self::assertSame('', Esc::css($value), "Expected non-stringable '{$type}' to return empty string from " . Esc::class . "::css");
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
            self::assertSame($expected, Esc::html($value), "Expected stringable '{$name}' to return '{$expected}' string from " . Esc::class . "::html");
            self::assertSame($expected, Esc::htmlAttr($value), "Expected stringable '{$name}' to return '{$expected}' string from " . Esc::class . "::htmlAttr");
            self::assertSame($expected, Esc::js($value), "Expected stringable '{$name}' to return '{$expected}' string from " . Esc::class . "::js");
            self::assertSame($expected, Esc::css($value), "Expected stringable '{$name}' to return '{$expected}' string from " . Esc::class . "::css");
        }
    }
}
