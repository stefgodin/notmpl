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
     * @param array $parameters - The parameters to be passed to the template
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
     * Slots of the sub template are not shared and cannot be extended.
     *
     * @param string $template - The embedded template to render
     * @param array $parameters - Specified additional parameters
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function embed(string $template, array $parameters = []): void
    {
        RenderContextStack::instance()->getCurrentContext()->embed($template, $parameters);
    }
    
    /**
     * Starts a render context within the current render context.
     * Slots of the merged template are shared and can be overwritten.
     *
     * @param string $template - The merged template to render
     * @param array $parameters - Specified additional parameters
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function merge(string $template, array $parameters = []): void
    {
        RenderContextStack::instance()->getCurrentContext()->merge($template, $parameters);
    }
    
    /**
     * Starts the context of a slot to allow replacement of the slot content on demand.
     * If a slot with the same name already exists, it replaces the content of the existing slot with this one.
     *
     * @param string $name
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function slot(string $name): void
    {
        RenderContextStack::instance()->getCurrentContext()->startSlot($name);
    }
    
    /**
     * Renders the content of the parent slot
     *
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function parentSlot(): void
    {
        RenderContextStack::instance()->getCurrentContext()->renderParentSlot();
    }
    
    /**
     * Ends the context of the last declared slot.
     *
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function endSlot(): void
    {
        RenderContextStack::instance()->getCurrentContext()->endSlot();
    }
}