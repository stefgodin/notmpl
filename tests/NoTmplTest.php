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
use StefGodin\NoTmpl\Engine\DefaultFileHandlers;
use StefGodin\NoTmpl\Engine\EngineException;
use StefGodin\NoTmpl\NoTmpl;
use function PHPUnit\Framework\assertSame;
use function StefGodin\NoTmpl\component;
use function StefGodin\NoTmpl\component_end;
use function StefGodin\NoTmpl\esc;
use function StefGodin\NoTmpl\has_slot;
use function StefGodin\NoTmpl\parent_slot;
use function StefGodin\NoTmpl\slot;
use function StefGodin\NoTmpl\slot_end;
use function StefGodin\NoTmpl\text;
use function StefGodin\NoTmpl\text_end;
use function StefGodin\NoTmpl\use_repeat_slots;
use function StefGodin\NoTmpl\use_slot;
use function StefGodin\NoTmpl\use_slot_end;

class NoTmplTest extends TestCase
{
    /** @test */
    public function set_global_params(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/set_global_params'])
            ->setRenderGlobalParams(['test' => 1, 'test2' => 2])
            ->setRenderGlobalParams(['test3' => 3, 'test4' => 4], true);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/set_global_params/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php', ['test3' => 5])),
        );
    }
    
    /** @test */
    public function set_directories(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/set_directories/1'])
            ->setDirectories([__DIR__ . '/templates/set_directories/2'], true)
            ->setDirectories([__DIR__ . '/templates/set_directories/3']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/set_directories/expected1.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/set_directories/expected2.html'),
            self::removeWhitespace($noTmpl->render('index2.php')),
        );
    }
    
    /** @test */
    public function set_aliases(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/set_aliases'])
            ->setAliases(['index' => 'main'])
            ->setAliases([], true)
            ->setAliases(['component' => 'main']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/set_aliases/expected.html'),
            self::removeWhitespace($noTmpl->render('index')),
        );
    }
    
    /** @test */
    public function auto_resolve_extensions(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/auto_resolve_extensions'])
            ->setAutoResolvedExtensions(['md'])
            ->setAutoResolvedExtensions(['html'], true)
            ->setAutoResolvedExtensions(['php']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/auto_resolve_extensions/expected.html'),
            self::removeWhitespace($noTmpl->render('index')),
        );
    }
    
    /** @test */
    public function set_file_handlers(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/set_file_handlers'])
            ->setFileHandlers(['/^.+\.md$/' => DefaultFileHandlers::raw(...)])
            ->setFileHandlers(['/^.+\.php$/' => DefaultFileHandlers::raw(...)], true);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/set_file_handlers/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
    }
    
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
    
    /** @test */
    public function no_handler(): void
    {
        $this->expectException(EngineException::class);
        $this->expectExceptionCode(EngineException::NO_FILE_HANDLER);
        $noTmpl = (new NoTmpl())->addDirectory(__DIR__ . '/templates/no_handler');
        $noTmpl->render('index.md');
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
            'use_repeat_slots' => use_repeat_slots(...),
            'has_slot' => has_slot(...),
            'esc' => fn() => esc(''),
            'text' => text(...),
            'text_end' => text_end(...),
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
    
    /** @test */
    public function complex_example(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/complex_example'])
            ->setAlias('components/logo', 'logo')
            ->setAlias('components/footer', 'footer')
            ->addAutoResolvedExtensions('html')
            ->setRenderGlobalParam('products', [
                ['id' => 1, 'name' => 'Cookies', 'price' => 10],
                ['id' => 2, 'name' => 'Potato', 'price' => 1],
                ['id' => 3, 'name' => 'Bread', 'price' => 25],
                ['id' => 4, 'name' => 'Eggs', 'price' => 5],
            ]);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/complex_example/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php', ['title' => 'Page <span attr="injection"></span>'])),
        );
    }
    
    /** @test */
    public function repeat_slots(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/repeat_slots']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/repeat_slots/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
    }
    
    /** @test */
    public function empty_repeat_slots(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/empty_repeat_slots']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/empty_repeat_slots/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
    }
    
    /** @test */
    public function repeat_slots_outside(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/repeat_slots_outside']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/repeat_slots_outside/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
    }
    
    /** @test */
    public function root_slot(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/root_slot']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/root_slot/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
    }
    
    /** @test */
    public function parent_slot(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/parent_slot']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/parent_slot/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
    }
    
    /** @test */
    public function parent_slot_outside(): void
    {
        self::expectException(EngineException::class);
        self::expectExceptionCode(EngineException::INVALID_TREE_STRUCTURE);
        
        (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/parent_slot_outside'])
            ->render('index.php');
    }
    
    /** @test */
    public function use_slot_outside(): void
    {
        self::expectException(EngineException::class);
        self::expectExceptionCode(EngineException::INVALID_TREE_STRUCTURE);
        
        (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/use_slot_outside'])
            ->render('index.php');
    }
    
    /** @test */
    public function has_slot(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/has_slot']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/has_slot/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
    }
    
    /** @test */
    public function has_no_slot(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/has_no_slot']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/has_no_slot/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
    }
    
    /** @test */
    public function has_slot_outside(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/has_slot_outside']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/has_slot_outside/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
    }
    
    /** @test */
    public function had_slot(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/had_slot']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/had_slot/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
    }
    
    /** @test */
    public function ob_left_open(): void
    {
        self::expectException(EngineException::class);
        self::expectExceptionCode(EngineException::ILLEGAL_BUFFER_ACTION);
        
        (new NoTmpl())
            ->addDirectory(__DIR__ . '/templates/ob_left_open')
            ->render('index.php');
    }
    
    /** @test */
    public function ob_early_close(): void
    {
        self::expectException(EngineException::class);
        self::expectExceptionCode(EngineException::ILLEGAL_BUFFER_ACTION);
        
        (new NoTmpl())
            ->addDirectory(__DIR__ . '/templates/ob_early_close')
            ->render('index.php');
    }
    
    /** @test */
    public function empty_default_slot(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/empty_default_slot']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/empty_default_slot/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
    }
    
    /** @test */
    public function esc_vars(): void
    {
        $noTmpl = (new NoTmpl())
            ->setDirectories([__DIR__ . '/templates/esc_vars']);
        
        self::assertSame(
            self::tmpl(__DIR__ . '/templates/esc_vars/expected.html'),
            self::removeWhitespace($noTmpl->render('index.php')),
        );
        
    }
    
    private static function tmpl(string $file): string
    {
        return self::removeWhitespace(file_get_contents($file));
    }
    
    private static function removeWhitespace(string $value): string
    {
        return preg_replace("/\s+(<)|(>)\s+/", "$1$2", $value);
    }
}