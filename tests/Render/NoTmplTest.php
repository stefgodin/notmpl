<?php


namespace Stefmachine\NoTmpl\Tests\Render;

use PHPUnit\Framework\TestCase;
use Stefmachine\NoTmpl\Exception\RenderException;
use Stefmachine\NoTmpl\Render\NoTmpl;
use function PHPUnit\Framework\assertSame;

class NoTmplTest extends TestCase
{
    protected function setUp(): void
    {
        NoTmpl::config()->addTemplateDirectory(__DIR__ . '/../templates');
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
    public function should_render_overwritten_block(): void
    {
        $result = NoTmpl::render('overwritten_block.php');
        assertSame("<div>test</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_overwritten_block_in_merge_template(): void
    {
        $result = NoTmpl::render('overwritten_block_merge.php');
        assertSame("<div>test</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_all_blocks_in_embed_template(): void
    {
        $result = NoTmpl::render('overwritten_block_embed.php');
        assertSame("<div>my_block</div><div>test</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_nested_overwritten_blocks(): void
    {
        $result = NoTmpl::render('nested_blocks.php');
        assertSame("<div>Before</div><div>Overwritten</div><div>After</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_nested_overwritten_blocks_from_merge_template(): void
    {
        $result = NoTmpl::render('merge_nested_blocks.php');
        assertSame("<div>Before</div><div>Overwritten</div><div>After</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_nested_overwritten_blocks_in_merge_template(): void
    {
        $result = NoTmpl::render('merge_nested_blocks_within.php');
        assertSame("<div>test</div><div>Before</div><div>After</div>", self::cleanupValue($result));
    }
    
    /** @test */
    public function should_render_last_overwritten_block(): void
    {
        $result = NoTmpl::render('multiple_block_overwrite.php');
        assertSame("<div>test3</div>", self::cleanupValue($result));
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
    public function should_throw_on_non_ended_block(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('non_ended_block.php');
    }
    
    /** @test */
    public function should_throw_on_non_ended_block_in_merge(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('non_ended_block_merge.php');
    }
    
    /** @test */
    public function should_throw_on_non_ended_block_in_embed(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::render('non_ended_block_embed.php');
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
    public function should_throw_when_using_block_out_of_render_context(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::block('test');
    }
    
    /** @test */
    public function should_throw_when_using_end_block_out_of_render_context(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::endBlock();
    }
    
    /** @test */
    public function should_throw_when_using_parent_block_out_of_render_context(): void
    {
        $this->expectException(RenderException::class);
        NoTmpl::parentBlock();
    }
}
