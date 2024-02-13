<?php


namespace StefGodin\NoTmpl\Tests\Escape;

use PHPUnit\Framework\TestCase;
use stdClass;
use StefGodin\NoTmpl\Escape\Esc;
use StefGodin\NoTmpl\Render\NoTmpl;
use function StefGodin\NoTmpl\Escape\esc_css;
use function StefGodin\NoTmpl\Escape\esc_html;
use function StefGodin\NoTmpl\Escape\esc_html_attr;
use function StefGodin\NoTmpl\Escape\esc_js;

class EscTest extends TestCase
{
    protected function setUp(): void
    {
        NoTmpl::config()->setEscaperEncoding('utf-8');
    }
    
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
            self::assertSame('', esc_html($value), "Expected non-stringable '{$type}' to return empty string from " . Esc::class . "::html");
            self::assertSame('', esc_html_attr($value), "Expected non-stringable '{$type}' to return empty string from " . Esc::class . "::htmlAttr");
            self::assertSame('', esc_js($value), "Expected non-stringable '{$type}' to return empty string from " . Esc::class . "::js");
            self::assertSame('', esc_css($value), "Expected non-stringable '{$type}' to return empty string from " . Esc::class . "::css");
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
            self::assertSame($expected, esc_html_attr($value), "Expected stringable '{$name}' to return '{$expected}' string from " . Esc::class . "::htmlAttr");
            self::assertSame($expected, esc_js($value), "Expected stringable '{$name}' to return '{$expected}' string from " . Esc::class . "::js");
            self::assertSame($expected, esc_css($value), "Expected stringable '{$name}' to return '{$expected}' string from " . Esc::class . "::css");
        }
    }
}
