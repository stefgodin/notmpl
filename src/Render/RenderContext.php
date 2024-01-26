<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Exception\RenderException;
use Throwable;

/**
 * @internal
 */
class RenderContext
{
    protected static array $globalParameters = [];
    
    public static function setGlobal(string $name, mixed $value): void
    {
        self::$globalParameters[$name] = $value;
    }
    
    public static function setGlobals(array $values): void
    {
        foreach($values as $key => $value) {
            self::setGlobal($key, $value);
        }
    }
    
    /** @var RenderBlock[] */
    protected array $blocks;
    protected OutputContext|null $outputContext;
    
    public function __construct(
        protected RenderContextStack $renderContextStack,
        protected array              $parameters = [],
    )
    {
        $this->blocks = [];
        $this->outputContext = null;
    }
    
    public function render(string $template): string
    {
        try {
            $this->renderContextStack->pushContext($this);
            
            if($this->outputContext !== null) {
                throw new RenderException("Rendering context of '{$template}' already rendered.");
            }
            
            $this->outputContext = new OutputContext("Render {$template}");
            
            $this->outputContext->open();
            _isolate_render($template, self::mergeParameters($this->parameters));
            
            foreach($this->blocks as $block) {
                if($block->isStarted() && !$block->isEnded()) {
                    throw new RenderException("Rendering context of '{$template}' has non-ended block '{$block->getName()}'.");
                }
            }
            
            $output = $this->outputContext->close()->getOutput();
            for($outputChanged = true; $outputChanged; $outputChanged = false) {
                foreach($this->blocks as $block) {
                    $newOutput = str_replace($block->getMarkup(), $block->getOutput(), $output);
                    $outputChanged = $outputChanged || $newOutput !== $output;
                    $output = $newOutput;
                }
            }
            
            $this->renderContextStack->popContext();
            
            return $output;
        } catch(Throwable $ex) {
            if($this->outputContext->isOpen()) {
                $this->outputContext->forceClose();
            }
            
            if($this->renderContextStack->hasContext() && $this->renderContextStack->getCurrentContext() === $this) {
                $this->renderContextStack->popContext();
            }
            
            throw $ex;
        }
    }
    
    public function extend(string $template, array|null $parameters = null): string
    {
        $outputContext = new OutputContext("Extend $template");
        $outputContext->open();
        _isolate_render($template, self::mergeParameters($parameters ?? $this->parameters));
        return $outputContext->close()->getOutput();
    }
    
    public function embed(string $template, array|null $parameters = null): string
    {
        $context = new RenderContext(
            RenderContextStack::instance(),
            $parameters ?? $this->parameters
        );
        return $context->render($template);
    }
    
    public function block(string $name): string
    {
        $block = new RenderBlock($name);
        foreach($this->blocks as $oldBlock) {
            if($oldBlock->getName() === $name) {
                $oldBlock->replaceWith($block);
                break;
            }
        }
        
        $this->blocks[] = $block;
        $block->start();
        return '';
    }
    
    public function endBlock(): string
    {
        foreach(array_reverse($this->blocks) as $block) {
            if(!$block->isEnded()) {
                $block->end();
                
                if(!$block->isReplacing()) {
                    return $block->getMarkup();
                }
                
                return '';
            }
        }
        
        return '';
    }
    
    private static function mergeParameters(array $parameters = []): array
    {
        return array_merge(
            self::$globalParameters,
            $parameters,
        );
    }
}

/**
 * @param string $template
 * @param array $params
 * @return void
 *
 * @internal
 */
function _isolate_render(): void
{
    extract(func_get_arg(1));
    $file = func_get_arg(0);
    if(!file_exists($file)) {
        throw new RenderException("File '{$file}' not found for rendering.");
    }
    
    if(pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        require $file;
        return;
    }
    
    echo file_get_contents($file);
}