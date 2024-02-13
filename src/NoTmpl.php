<?php


namespace StefGodin\NoTmpl;

use StefGodin\NoTmpl\Engine\EngineException;
use StefGodin\NoTmpl\Engine\RenderContext;
use StefGodin\NoTmpl\Engine\TemplateResolver;
use Throwable;

/**
 * Static object class to regroup all rendering functions of the NoTmpl library
 */
class NoTmpl
{
    private static array $renderContextStack = [];
    private static Config $config;
    
    /**
     * Returns the NoTmpl config instance to allow configuration of the engine
     *
     * @return Config
     */
    public static function config(): Config
    {
        return self::$config ??= new Config();
    }
    
    /**
     * Renders a template content with given parameters as variables and returns the resulting rendered content as a string.
     *
     * @param string $template - The template to render
     * @param array $parameters - The parameters to be passed to the template
     * @return string
     * @throws EngineException
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function render(string $template, array $parameters = []): string
    {
        $templateResolver = new TemplateResolver(
            self::config()->getTemplateDirectories(),
            self::config()->getTemplateAliases(),
        );
        
        try {
            $rCtx = self::$renderContextStack[] = new RenderContext(
                $templateResolver,
                self::config()->getRenderGlobalParams()
            );
            $result = $rCtx->render($template, $parameters);
            array_pop(self::$renderContextStack);
        } catch(Throwable $e) {
            array_pop(self::$renderContextStack);
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $e;
        }
        
        return $result;
    }
    
    /**
     * Starts a component block and loads a specific template for it.
     * Slots of the component are not shared with the parent component which allows reuse of names.
     *
     * @param string $name - The subcomponent to render
     * @param array $parameters - Specified additional parameters
     * @return void
     * @throws EngineException
     */
    public static function component(string $name, array $parameters = []): void
    {
        self::getCurrentRenderContext(__METHOD__)->component($name, $parameters);
    }
    
    /**
     * Ends the last open component tag
     *
     * @return void
     * @throws EngineException
     */
    public static function componentEnd(): void
    {
        self::getCurrentRenderContext(__METHOD__)->componentEnd();
    }
    
    /**
     * Starts the context of a slot within a component template to allow replacement of the slot content on demand.
     * Slot names must be unique within a component.
     *
     * @param string $name
     * @return void
     * @throws EngineException
     */
    public static function slot(string $name = 'default'): void
    {
        self::getCurrentRenderContext(__METHOD__)->slot($name);
    }
    
    /**
     * Ends the context of the last open slot tag.
     *
     * @return void
     * @throws EngineException
     */
    public static function slotEnd(): void
    {
        self::getCurrentRenderContext(__METHOD__)->slotEnd();
    }
    
    /**
     * Starts the context of a use-slot to overwrite the internal content of a slot within a component.
     * Use-slot names must be unique within a component.
     *
     * Usage of 'default' slot is optional as a discrete use-slot:default is created for content put directly
     * between a component tags.
     *
     * @param string $name
     * @return void
     * @throws \StefGodin\NoTmpl\Engine\EngineException
     */
    public static function useSlot(string $name = 'default'): void
    {
        self::getCurrentRenderContext(__METHOD__)->useSlot($name);
    }
    
    /**
     * Renders the content of the used parent slot
     *
     * @return void
     * @throws EngineException
     */
    public static function parentSlot(): void
    {
        self::getCurrentRenderContext(__METHOD__)->parentSlot();
    }
    
    /**
     * Ends the context of the last open use-slot tag
     *
     * @return void
     * @throws EngineException
     */
    public static function useSlotEnd(): void
    {
        self::getCurrentRenderContext(__METHOD__)->useSlotEnd();
    }
    
    /**
     * @param string $fn
     * @return RenderContext
     * @throws \StefGodin\NoTmpl\Engine\EngineException
     */
    private static function getCurrentRenderContext(string $fn): RenderContext
    {
        if(empty(self::$renderContextStack)) {
            throw new EngineException(
                "Cannot use '{$fn}' outside of render context.",
                EngineException::CTX_NO_CONTEXT
            );
        }
        
        return self::$renderContextStack[count(self::$renderContextStack) - 1];
    }
}