<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Config\Config;

/**
 * Static object class to regroup all rendering functions of the NoTmpl library
 */
class NoTmpl
{
    /**
     * Returns the NoTmpl config instance to allow configuration of the engine
     *
     * @return Config
     */
    public static function config(): Config
    {
        return Config::instance();
    }
    
    /**
     * Renders a template content with given parameters as variables and returns the resulting rendered content as a string.
     *
     * @param string $template - The template to render
     * @param array $parameters
     * @return string
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function render(string $template, array $parameters = []): string
    {
        $context = new RenderContext(
            RenderContextStack::instance(),
            $parameters
        );
        return $context->render($template);
    }
    
    /**
     * Starts a new separate render context to embed a template into the current render context.
     * Blocks of the sub template are not shared and cannot be extended.
     *
     * @param string $template
     * @param array|null $parameters
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function embed(string $template, array|null $parameters = null): void
    {
        RenderContextStack::instance()->getCurrentContext()->embed($template, $parameters);
    }
    
    /**
     * Starts a render context within the current render context.
     * Blocks of the merged template are shared and can be overwritten.
     *
     * @param string $template - The merged template to render
     * @param array|null $parameters - Specified parameters or the parameters of the current context
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function merge(string $template, array|null $parameters = null): void
    {
        RenderContextStack::instance()->getCurrentContext()->merge($template, $parameters);
    }
    
    /**
     * Starts the context of a block to allow replacement of the block content on demand.
     * If a block with the same name already exists, it replaces the content of the existing block with this one.
     *
     * @param string $name
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function block(string $name): void
    {
        RenderContextStack::instance()->getCurrentContext()->startBlock($name);
    }
    
    /**
     * Renders the content of the parent block
     *
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function parentBlock(): void
    {
        RenderContextStack::instance()->getCurrentContext()->renderParentBlock();
    }
    
    /**
     * Ends the context of the last declared block.
     *
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function endBlock(): void
    {
        RenderContextStack::instance()->getCurrentContext()->endBlock();
    }
}