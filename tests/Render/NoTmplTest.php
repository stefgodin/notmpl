<?php


namespace Stefmachine\NoTmpl\Tests\Render;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Stefmachine\NoTmpl\Exception\RenderException;
use Stefmachine\NoTmpl\Render\NoTmpl;
use function PHPUnit\Framework\assertSame;

class NoTmplTest extends TestCase
{
    protected function setUp(): void
    {
        NoTmpl::config()
            ->addTemplateDirectory(__DIR__ . '/../templates')
            ->addTemplateDirectory(__DIR__ . '/../templates/subdir');
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
    public function should_render_overwritten_slot_in_merge_template(): void
    {
        $result = NoTmpl::render('overwritten_slot_merge.php');
        assertSame("<div>test</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_all_slots_in_embed_template(): void
    {
        $result = NoTmpl::render('overwritten_slot_embed.php');
        assertSame("<div>my_slot</div><div>test</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_nested_overwritten_slots(): void
    {
        $result = NoTmpl::render('nested_slots.php');
        assertSame("<div>Before</div><div>Overwritten</div><div>After</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_nested_overwritten_slots_from_merge_template(): void
    {
        $result = NoTmpl::render('merge_nested_slots.php');
        assertSame("<div>Before</div><div>Overwritten</div><div>After</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_nested_overwritten_slots_in_merge_template(): void
    {
        $result = NoTmpl::render('merge_nested_slots_within.php');
        assertSame("<div>test</div><div>Before</div><div>After</div>", self::cleanupValue($result));
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
        $result = NoTmpl::render('subdir.php');
        assertSame("<div>test</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_throw_on_missing_template(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('missing_template.php');
    }
    
    /** @test */
    public function should_throw_on_missing_merge_template(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('missing_merge.php');
    }
    
    /** @test */
    public function should_throw_on_missing_embed_template(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('missing_embed.php');
    }
    
    /** @test */
    public function should_throw_on_non_ended_slot(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('non_ended_slot.php');
    }
    
    /** @test */
    public function should_throw_on_non_ended_slot_in_merge(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('non_ended_slot_merge.php');
    }
    
    /** @test */
    public function should_throw_on_non_ended_slot_in_embed(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('non_ended_slot_embed.php');
    }
    
    /** @test */
    public function should_throw_when_using_merge_out_of_render_context(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::merge('basic.php');
    }
    
    /** @test */
    public function should_throw_when_using_embed_out_of_render_context(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::embed('basic.php');
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
    public function should_cleanup_and_rethrow_on_exception_in_embed(): void
    {
        $expectedLevel = ob_get_level();
        try {
            NoTmpl::render('throw_embed.php');
        } catch(RuntimeException) {
        }
        assertSame($expectedLevel, ob_get_level(), "Output buffer was not cleaned up properly.");
    }
}
