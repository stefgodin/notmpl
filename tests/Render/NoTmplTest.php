<?php


namespace Stefmachine\CNoTmpl\Tests\Render;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Stefmachine\NoTmpl\Exception\RenderException;
use Stefmachine\NoTmpl\Render\NoTmpl;
use function PHPUnit\Framework\assertSame;
use function Stefmachine\NoTmpl\Render\render;

class NoTmplTest extends TestCase
{
    protected function setUp(): void
    {
        NoTmpl::config()
            ->addTemplateDirectory(__DIR__ . '/../templates');
    }
    
    private static function cleanupValue(string $value): string
    {
        return strtr($value, ["\n" => "", "\r" => "", " " => ""]);
    }
    
    /** @test */
    public function should_render_basic_php_file(): void
    {
        $result = NoTmpl::render('basic.php');
        assertSame("<div>Basic_php</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_basic_html_file(): void
    {
        $result = NoTmpl::render('basic.html');
        assertSame("<div>Basic_html</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_php_variables(): void
    {
        $result = NoTmpl::render('basic_variables.php', [
            'testVar' => 'test',
            'otherTestVar' => 'test2',
        ]);
        assertSame("<div>test</div><div>test2</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_overwritten_slot(): void
    {
        $result = NoTmpl::render('overwritten_slot.php');
        assertSame("<div>test</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_overwritten_slot_in_component(): void
    {
        $result = NoTmpl::render('overwritten_slot_component.php');
        assertSame("<div>test</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_nested_overwritten_slots(): void
    {
        $result = NoTmpl::render('nested_slots.php');
        assertSame("<div>before</div><div>overwritten</div><div>after</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_nested_overwritten_slots_from_component_template(): void
    {
        $result = NoTmpl::render('component_nested_slots.php');
        assertSame("<div>before</div><div>overwritten</div><div>after</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_last_overwritten_slot(): void
    {
        $result = NoTmpl::render('multiple_slot_overwrite.php');
        assertSame("<div>test3</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_find_template_from_multiple_configured_directory(): void
    {
        NoTmpl::config()->addTemplateDirectory(__DIR__ . '/../templates/subdir');
        $result = NoTmpl::render('subdir.php');
        assertSame("<div>test</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_find_template_from_alias(): void
    {
        NoTmpl::config()->setTemplateAlias('basic.php', 'my_alias');
        $result = NoTmpl::render('my_alias');
        assertSame("<div>Basic_php</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_parent_slot_of_component(): void
    {
        $result = NoTmpl::render('parent_slot.php');
        assertSame("<div>before</div><div>my_slot</div><div>after</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_throw_on_missing_template(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('missing_template.php');
    }
    
    /** @test */
    public function should_throw_on_missing_component_template(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('missing_component.php');
    }
    
    /** @test */
    public function should_throw_on_non_ended_component(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('non_ended_component.php');
    }
    
    /** @test */
    public function should_throw_on_non_ended_slot(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('non_ended_slot.php');
    }
    
    /** @test */
    public function should_throw_on_non_ended_slot_in_component(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('non_ended_slot_component.php');
    }
    
    /** @test */
    public function should_throw_when_using_component_out_of_render_context(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::component('basic.php');
    }
    
    /** @test */
    public function should_throw_when_using_slot_out_of_render_context(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::slot('test');
    }
    
    /** @test */
    public function should_throw_when_using_end_slot_out_of_render_context(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::endSlot();
    }
    
    /** @test */
    public function should_throw_when_using_parent_slot_out_of_render_context(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::parentSlot();
    }
    
    /** @test */
    public function should_cleanup_and_rethrow_on_exception(): void
    {
        $expectedLevel = ob_get_level();
        try {
            NoTmpl::render('throw.php');
        } catch(RuntimeException) {
        }
        assertSame($expectedLevel, ob_get_level(), "Output buffer was not cleaned up properly.");
    }
    
    /** @test */
    public function should_cleanup_and_rethrow_on_exception_in_subcomponent(): void
    {
        $expectedLevel = ob_get_level();
        try {
            NoTmpl::render('throw_subcomponent.php');
        } catch(RuntimeException) {
        }
        assertSame($expectedLevel, ob_get_level(), "Output buffer was not cleaned up properly.");
    }
    
    /** @test */
    public function should_throw_on_early_component_end(): void
    {
        $this->expectException(RenderException::class);
        render('early_component_end.php');
    }
    
    /** @test */
    public function should_throw_on_early_subcomponent_end(): void
    {
        $this->expectException(RenderException::class);
        render('early_subcomponent_end.php');
    }
}
