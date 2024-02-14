<?php


namespace StefGodin\NoTmpl\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use StefGodin\NoTmpl\Engine\EngineException;
use StefGodin\NoTmpl\NoTmpl;
use function PHPUnit\Framework\assertSame;
use function StefGodin\NoTmpl\component;
use function StefGodin\NoTmpl\component_end;
use function StefGodin\NoTmpl\parent_slot;
use function StefGodin\NoTmpl\slot;
use function StefGodin\NoTmpl\slot_end;
use function StefGodin\NoTmpl\use_slot;
use function StefGodin\NoTmpl\use_slot_end;

class NoTmplTest extends TestCase
{
    /** @test */
    public function component_slot_override(): void
    {
        NoTmpl::config()
            ->setTemplateDirectories([__DIR__ . '/templates/component_slot_override'])
            ->setTemplateAliases([
                'page' => 'page_component.php',
                'footer' => 'footer_component.php',
            ]);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/component_slot_override/expected.html'),
            self::removeWhitespace(NoTmpl::render('index.php', ['title' => 'hello'])),
        );
    }
    
    /**
     * @test
     * @noinspection PhpRedundantCatchClauseInspection
     */
    public function call_out_of_context(): void
    {
        $fns = [
            'component' => fn() => component('test'),
            'component_end' => component_end(...),
            'slot' => slot(...),
            'slot_end' => slot_end(...),
            'use_slot' => use_slot(...),
            'parent_slot' => parent_slot(...),
            'use_slot_end' => use_slot_end(...),
            'NoTmpl::component' => fn() => NoTmpl::component('test'),
            'NoTmpl::componentEnd' => NoTmpl::componentEnd(...),
            'NoTmpl::slot' => NoTmpl::slot(...),
            'NoTmpl::slotEnd' => NoTmpl::slotEnd(...),
            'NoTmpl::useSlot' => NoTmpl::useSlot(...),
            'NoTmpl::parentSlot' => NoTmpl::parentSlot(...),
            'NoTmpl::useSlotEnd' => NoTmpl::useSlotEnd(...),
        ];
        
        foreach($fns as $name => $fn) {
            try {
                $fn();
                self::fail("Function '{$name}' did not fail when called out of context");
            } catch(EngineException $e) {
                assertSame(EngineException::CTX_NO_CONTEXT, $e->getCode(),
                    "Function '{$name}' failed but with wrong error code '{$e->getCode()}'");
            }
        }
    }
    
    /**
     * @test
     * @noinspection PhpRedundantCatchClauseInspection
     */
    public function fail_render_cleanup(): void
    {
        $expectedLevel = ob_get_level();
        
        NoTmpl::config()
            ->setTemplateDirectories([__DIR__ . '/templates/fail_render_cleanup']);
        
        try {
            NoTmpl::render('index.php');
        } catch(RuntimeException) {
            assertSame($expectedLevel, ob_get_level(), "Failing render function did not properly cleanup output buffers");
        }
    }
    
    private static function tmpl(string $file): string
    {
        return self::removeWhitespace(file_get_contents($file));
    }
    
    private static function removeWhitespace(string $value): string
    {
        return preg_replace("/\s/", "", $value);
    }
}