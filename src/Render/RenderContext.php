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
    private array $blocks;
    private OutputContextStack $outputContextStack;
    
    public function __construct(
        private RenderContextStack $renderContextStack,
        private array              $params = [],
    )
    {
        $this->blocks = [];
        $this->outputContextStack = new OutputContextStack();
    }
    
    /**
     * @param string $template
     * @return string
     * @throws RenderException
     */
    public function render(string $template): string
    {
        if($this->outputContextStack->hasContext()) {
            throw new RenderException("Render context is already rendering '{$this->outputContextStack->getMainContext()->getName()}'.");
        }
        
        try {
            $template = $this->findTemplateFile($template);
            
            $this->renderContextStack->pushContext($this);
            $oc = $this->outputContextStack
                ->pushContext(OutputContext::create("Render '{$template}'"))
                ->getMainContext();
            $oc->open()
                ->includeFile($template, array_merge($this->getConfig()->getRenderGlobalParams(), $this->params))
                ->close();
            
            $output = $oc->getOutput();
            for($outputChanged = true; $outputChanged; $outputChanged = false) {
                foreach($this->blocks as $block) {
                    $newOutput = str_replace($block->getMarkup(), $block->getOutput(), $output);
                    $outputChanged = $outputChanged || $newOutput !== $output;
                    $output = $newOutput;
                }
            }
            
            $this->outputContextStack->popContext();
            $this->renderContextStack->popContext();
            
            return $output;
        } catch(Throwable $ex) {
            if($this->outputContextStack->hasContext()) {
                $oc = $this->outputContextStack->getMainContext();
                if($oc->isOpen()) {
                    $oc->forceClose();
                }
                
                while($this->outputContextStack->hasContext()) {
                    $this->outputContextStack->popContext();
                }
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
    public function merge(string $template, array|null $parameters = null): void
    {
        $template = $this->findTemplateFile($template);
        $this->outputContextStack->pushContext(OutputContext::create("Merge {$template}"));
        $extendedContent = $this->outputContextStack->getCurrentContext()
            ->open()
            ->includeFile($template, array_merge($this->getConfig()->getRenderGlobalParams(), $parameters ?? $this->params))
            ->close()
            ->getOutput();
        
        $this->outputContextStack->popContext();
        $this->outputContextStack->getCurrentContext()->writeContent($extendedContent);
    }
    
    /**
     * @param string $template
     * @return string
     * @throws RenderException
     */
    private function findTemplateFile(string $template): string
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
        $context = new RenderContext(
            RenderContextStack::instance(),
            $parameters ?? $this->params
        );
        $embedContent = $context->render($template);
        $this->outputContextStack->getCurrentContext()->writeContent($embedContent);
    }
    
    /**
     * @param string $name
     * @return void
     * @throws RenderException
     */
    public function startBlock(string $name): void
    {
        $block = new RenderBlock($this->outputContextStack, $name);
        foreach($this->blocks as $oldBlock) {
            if($oldBlock->getName() === $name) {
                if(!$oldBlock->isEnded()) {
                    throw new RenderException("Cannot overwrite non-ended block '{$name}' in render context '{$this->outputContextStack->getCurrentContext()->getName()}'.");
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
        foreach(array_reverse($this->blocks) as $block) {
            if(!$block->isEnded()) {
                if(!$block->isReplacing()) {
                    throw new RenderException("Block '{$block->getName()}' is not extending a parent block in render context '{$this->outputContextStack->getCurrentContext()->getName()}'.");
                }
                
                $this->outputContextStack->getCurrentContext()
                    ->writeContent($block->getParentOutput());
                return;
            }
        }
        
        throw new RenderException("There is no parent block to render in render context '{$this->outputContextStack->getCurrentContext()->getName()}'.");
    }
    
    /**
     * @return void
     * @throws RenderException
     */
    public function endBlock(): void
    {
        foreach(array_reverse($this->blocks) as $block) {
            if(!$block->isEnded()) {
                $block->end();
                
                if(!$block->isReplacing()) {
                    $this->outputContextStack->getCurrentContext()->writeContent($block->getMarkup());
                }
                
                return;
            }
        }
        
        throw new RenderException("There are no more blocks to end in render context '{$this->outputContextStack->getCurrentContext()->getName()}'.");
    }
}