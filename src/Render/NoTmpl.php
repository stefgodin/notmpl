<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Config\Config;
use Stefmachine\NoTmpl\Exception\RenderException;
use Throwable;

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
        $component = new Component(
            ComponentStack::instance(),
            TemplateFinder::instance()->findTemplate($template),
            $parameters
        );
        
        try {
            return $component->start()->end()->getOutput();
        } catch(Throwable $ex) {
            $component->cleanUp();
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $ex; // Forwarding errors
        }
    }
    
    /**
     * Starts a subcomponent block and loads a specific template for it.
     * Slots of the subcomponent are not shared with the parent component which allows reuse of names.
     *
     * @param string $template - The embedded template to render
     * @param array $parameters - Specified additional parameters
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function component(string $template, array $parameters = []): void
    {
        ComponentStack::instance()->getCurrent()
            ->component(
                TemplateFinder::instance()->findTemplate($template),
                $parameters,
            )->start();
    }
    
    /**
     * Ends the last subcomponent block
     *
     * @return void
     * @throws RenderException
     */
    public static function endComponent(): void
    {
        $current = ComponentStack::instance()->getCurrent();
        if(ComponentStack::instance()->getMain() === $current) {
            throw new RenderException("No more sub component to end.");
        }
        
        $current->end();
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
        ComponentStack::instance()->getCurrent()->startSlot($name);
    }
    
    /**
     * Renders the content of the parent slot
     *
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function parentSlot(): void
    {
        ComponentStack::instance()->getCurrent()->renderParentSlot();
    }
    
    /**
     * Ends the context of the last declared slot.
     *
     * @return void
     * @throws \Stefmachine\NoTmpl\Exception\RenderException
     */
    public static function endSlot(): void
    {
        ComponentStack::instance()->getCurrent()->endSlot();
    }
}