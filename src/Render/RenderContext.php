<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Config\ConfigInjectTrait;
use Stefmachine\NoTmpl\Exception\RenderException;
use Throwable;

/**
 * @internal
 */
class RenderContext
{
    use ConfigInjectTrait;
    
    /** @var RenderBlock[] */
    protected array $blocks;
    protected OutputContext|null $outputContext;
    
    public function __construct(
        protected RenderContextStack $renderContextStack,
        protected array              $params = [],
    )
    {
        $this->blocks = [];
        $this->outputContext = null;
    }
    
    /**
     * @param string $template
     * @return string
     * @throws RenderException
     */
    public function render(string $template): string
    {
        try {
            $this->renderContextStack->pushContext($this);
            
            if($this->outputContext !== null) {
                throw new RenderException("Rendering context of '{$template}' already rendered.");
            }
            
            $this->outputContext = new OutputContext("Render {$template}");
            
            $this->outputContext->open();
            $this->outputFileToBuffer($template, $this->params);
            
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
            
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $ex; // Forwarding errors
        }
    }
    
    /**
     * @param string $template
     * @param array|null $parameters
     * @return string
     * @throws RenderException
     */
    public function extend(string $template, array|null $parameters = null): string
    {
        $outputContext = new OutputContext("Extend $template");
        $outputContext->open();
        $this->outputFileToBuffer($template, $parameters ?? $this->params);
        return $outputContext->close()->getOutput();
    }
    
    /**
     * @param string $template
     * @param array|null $parameters
     * @return string
     * @throws RenderException
     */
    public function embed(string $template, array|null $parameters = null): string
    {
        $context = new RenderContext(
            RenderContextStack::instance(),
            $parameters ?? $this->params
        );
        return $context->render($template);
    }
    
    /**
     * @param string $name
     * @return string
     * @throws RenderException
     */
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
    
    /**
     * @return string
     * @throws RenderException
     */
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
    
    /**
     * @param string $file
     * @param array $params
     * @return void
     * @throws RenderException
     */
    protected function outputFileToBuffer(string $file, array $params = []): void
    {
        $file = func_get_arg(0);
        if(!file_exists(func_get_arg(0))) {
            throw new RenderException("File '{$file}' not found for rendering.");
        }
        
        if(pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            _isolate_php_render($file, array_merge($this->getConfig()->getRenderGlobalParams(), $params));
            return;
        }
        
        echo file_get_contents($file);
    }
}

/**
 * @param string $file
 * @param array $params
 * @return void
 */
function _isolate_php_render(): void
{
    extract(func_get_arg(1));
    require func_get_arg(0);
}