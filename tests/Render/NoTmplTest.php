<?php


namespace StefGodin\NoTmpl\Tests\Render;

use PHPUnit\Framework\TestCase;
use StefGodin\NoTmpl\NoTmpl;

class NoTmplTest extends TestCase
{
    /** @test */
    public function component_slot_override(): void
    {
        $expect = self::tmpl(function() { ?>
          <div>index_header</div>
          <div>
            <!--page_component-->
            <div>page_header</div>
            <div>
              <div>page_title_slot</div>
              <div>index_title_slot</div>
            </div>
            <div>
              <div>index_body_slot</div>
            </div>
            <div>
              <div>index_default_slot_top</div>
              <div>index_default_slot_bot</div>
            </div>
            <div>
              <div>page_footer_slot</div>
            </div>
          </div>
          <div>index_footer</div>
            <?php
        });
        
        NoTmpl::config()
            ->addTemplateDirectory(__DIR__ . '/templates/component_slot_override')
            ->setTemplateAlias('page_component.php', 'page');
        
        $result = self::removeWhitespace(NoTmpl::render('index.php'));
        
        self::assertSame(
            $expect,
            $result,
        );
    }
    
    private static function tmpl(callable $fn): string
    {
        ob_start();
        $fn();
        $tmpl = ob_get_clean();
        return self::removeWhitespace($tmpl);
    }
    
    private static function removeWhitespace(string $value): string
    {
        return preg_replace("/\s/", "", $value);
    }
}