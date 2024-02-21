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
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/component_slot_override'])
            ->setAliases([
                'page_component.php' => 'page',
                'footer_component.php' => 'footer',
            ]);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/component_slot_override/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php', ['title' => 'hello'])),
        );
    }
    
    /** @test */
    public function file_not_found(): void
    {
        $this->expectException(EngineException::class);
        $this->expectExceptionCode(EngineException::FILE_NOT_FOUND);
        $noTmpl = new NoTmpl();
        $noTmpl->render('not_found.php');
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
        ];
        
        foreach($fns as $name => $fn) {
            try {
                $fn();
                self::fail("Function '{$name}' did not fail when called out of context");
            } catch(EngineException $e) {
                assertSame(EngineException::NO_CONTEXT, $e->getCode(),
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
        
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/fail_render_cleanup']);
        
        try {
            $noTmpl->render('index.php');
        } catch(RuntimeException) {
            assertSame($expectedLevel, ob_get_level(), "Failing render function did not properly cleanup output buffers");
        }
    }
    
    /** @test */
    public function slot_bindings_scoping(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/slot_bindings_scoping']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/slot_bindings_scoping/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
    }
    
    /** @test */
    public function no_closing_tag(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/no_closing_tag']);
        
        $fns = [
            'component' => fn() => component('component.php'),
            'slot' => slot(...),
            'use_slot' => use_slot(...),
        ];
        
        $contexts = ['direct', 'nested'];
        
        foreach($contexts as $context) {
            foreach($fns as $name => $fn) {
                try {
                    $noTmpl->render("{$context}.php", ['tag' => $fn]);
                    self::fail("Calling function '{$name}' without a closing tag did not fail in '{$context}' context.");
                } catch(EngineException $e) {
                    assertSame(EngineException::INVALID_TREE_STRUCTURE, $e->getCode(),
                        "Calling function '{$name}' without a closing tag failed in '{$context}' context with wrong error code.",
                    );
                }
            }
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