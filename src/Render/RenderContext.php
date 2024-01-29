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
    protected OutputContext $outputContext;
    
    public function __construct(
        protected RenderContextStack $renderContextStack,
        protected array              $params = [],
    )
    {
        $this->blocks = [];
        $this->outputContext = OutputContext::create("Render unknown");
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
            
            $template = $this->findTemplateFile($template);
            if($this->outputContext->wasOpened()) {
                throw new RenderException("Rendering context of '{$template}' already rendering/rendered.");
            }
            
            $this->outputContext
                ->setName("Render {$template}")
                ->open()
                ->includeFile($template, array_merge($this->getConfig()->getRenderGlobalParams(), $this->params))
                ->close();
            
            $output = $this->outputContext->getOutput();
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
     * @return void
     * @throws RenderException
     */
    public function extend(string $template, array|null $parameters = null): void
    {
        if(!$this->outputContext->isOpen()) {
            throw new RenderException("Cannot extend closed render context.");
        }
        
        $template = $this->findTemplateFile($template);
        $extendedContent = OutputContext::create("Extend {$template}")
            ->open()
            ->includeFile($template, array_merge($this->getConfig()->getRenderGlobalParams(), $parameters ?? $this->params))
            ->close()
            ->getOutput();
        $this->outputContext->writeContent($extendedContent);
    }
    
    protected function findTemplateFile(string $template): string
    {
        if(file_exists($template)) {
            return $template;
        }
        
        $checkedPaths = ['"' . $template . '"'];
        foreach($this->getConfig()->getTemplateDirectories() as $dir) {
            $file = $dir . DIRECTORY_SEPARATOR . $template;
            if(file_exists($file)) {
                return $file;
            }
            $checkedPaths[] = '"' . $file . '"';
        }
        
        throw new RenderException(sprintf("Could not find template file '%s'. Checked for %s", $template, implode(', ', $checkedPaths)));
    }
    
    /**
     * @param string $template
     * @param array|null $parameters
     * @return void
     * @throws RenderException
     */
    public function embed(string $template, array|null $parameters = null): void
    {
        if(!$this->outputContext->isOpen()) {
            throw new RenderException("Cannot embed into a closed render context.");
        }
        
        $context = new RenderContext(
            RenderContextStack::instance(),
            $parameters ?? $this->params
        );
        $embedContent = $context->render($template);
        $this->outputContext->writeContent($embedContent);
    }
    
    /**
     * @param string $name
     * @return void
     * @throws RenderException
     */
    public function startBlock(string $name): void
    {
        if(!$this->outputContext->isOpen()) {
            throw new RenderException("Cannot add block into a closed render context.");
        }
        
        $block = new RenderBlock($name);
        foreach($this->blocks as $oldBlock) {
            if($oldBlock->getName() === $name) {
                if(!$oldBlock->isEnded()) {
                    throw new RenderException("Cannot extend non-ended block '{$name}'.");
                }
                
                $oldBlock->replaceWith($block);
                break;
            }
        }
        
        $this->blocks[] = $block;
        $block->start();
    }
    
    /**
     * @return void
     * @throws RenderException
     */
    public function renderParentBlock(): void
    {
        if(!$this->outputContext->isOpen()) {
            throw new RenderException("Cannot render parent block into a closed render context.");
        }
        
        foreach(array_reverse($this->blocks) as $block) {
            if(!$block->isEnded()) {
                
                if(!$block->isReplacing()) {
                    throw new RenderException("Block '{$block->getName()}' is not extending a parent block.");
                }
                
                $this->outputContext->writeContent($block->getParentOutput());
                return;
            }
        }
        
        throw new RenderException("There is no parent block to render.");
    }
    
    /**
     * @return void
     * @throws RenderException
     */
    public function endBlock(): void
    {
        if(!$this->outputContext->isOpen()) {
            throw new RenderException("Cannot end block from a closed render context.");
        }
        
        foreach(array_reverse($this->blocks) as $block) {
            if(!$block->isEnded()) {
                $block->end();
                
                if(!$block->isReplacing()) {
                    $this->outputContext->writeContent($block->getMarkup());
                }
                
                return;
            }
        }
        
        throw new RenderException("There are no more blocks to end.");
    }
}