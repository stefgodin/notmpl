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
    
    /** @var Slot[] */
    private array $slots;
    private OutputBufferStack $obStack;
    
    public function __construct(
        private RenderContextStack $renderContextStack,
        private array              $params = [],
    )
    {
        $this->slots = [];
        $this->obStack = new OutputBufferStack();
    }
    
    /**
     * @param string $template
     * @return string
     * @throws RenderException
     */
    public function render(string $template): string
    {
        if($this->obStack->hasBuffer()) {
            throw new RenderException("Render context is already rendering '{$this->obStack->getMain()->getName()}'.");
        }
        
        try {
            $template = $this->findTemplateFile($template);
            
            $this->renderContextStack->pushContext($this);
            $oc = $this->obStack
                ->push(OutputBuffer::create("Render '{$template}'"))
                ->getMain();
            $oc->open()
                ->includeFile($template, array_merge($this->getConfig()->getRenderGlobalParams(), $this->params))
                ->close();
            
            $output = $oc->getOutput();
            
            for($outputChanged = true; $outputChanged; $outputChanged = false) {
                foreach($this->slots as $slot) {
                    $newOutput = str_replace($slot->getMarkup(), $slot->getOutput(), $output);
                    $outputChanged = $outputChanged || $newOutput !== $output;
                    $output = $newOutput;
                }
            }
            
            $this->obStack->pop();
            $this->renderContextStack->popContext();
            
            return $output;
        } catch(Throwable $ex) {
            if($this->obStack->hasBuffer()) {
                $oc = $this->obStack->getMain();
                if($oc->isOpen()) {
                    $oc->forceClose();
                }
                
                while($this->obStack->hasBuffer()) {
                    $this->obStack->pop();
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
     * @param array $parameters
     * @return void
     * @throws RenderException
     */
    public function merge(string $template, array $parameters = []): void
    {
        $template = $this->findTemplateFile($template);
        $this->obStack->push(OutputBuffer::create("Merge {$template}"));
        $extendedContent = $this->obStack->getCurrent()
            ->open()
            ->includeFile($template, array_merge($this->getConfig()->getRenderGlobalParams(), $this->params, $parameters))
            ->close()
            ->getOutput();
        
        $this->obStack->pop();
        $this->obStack->getCurrent()->writeContent($extendedContent);
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
    public function embed(string $template, array $parameters = []): void
    {
        $context = new RenderContext(
            RenderContextStack::instance(),
            array_merge($this->params, $parameters)
        );
        $embedContent = $context->render($template);
        $this->obStack->getCurrent()->writeContent($embedContent);
    }
    
    /**
     * @param string $name
     * @return void
     * @throws RenderException
     */
    public function startSlot(string $name): void
    {
        $slot = new Slot($this->obStack, $name);
        foreach($this->slots as $oldSlot) {
            if($oldSlot->getName() === $name) {
                if(!$oldSlot->isEnded()) {
                    throw new RenderException("Cannot overwrite non-ended slot '{$name}' in render context '{$this->obStack->getCurrent()->getName()}'.");
                }
                
                $oldSlot->replaceWith($slot);
                break;
            }
        }
        
        $this->slots[] = $slot;
        $slot->start();
    }
    
    /**
     * @return void
     * @throws RenderException
     */
    public function renderParentSlot(): void
    {
        foreach(array_reverse($this->slots) as $slot) {
            if(!$slot->isEnded()) {
                if(!$slot->isReplacing()) {
                    throw new RenderException("Slot '{$slot->getName()}' is not extending a parent slot in render context '{$this->obStack->getCurrent()->getName()}'.");
                }
                
                $this->obStack->getCurrent()
                    ->writeContent($slot->getParentOutput());
                return;
            }
        }
        
        throw new RenderException("There is no parent slot to render in render context '{$this->obStack->getCurrent()->getName()}'.");
    }
    
    /**
     * @return void
     * @throws RenderException
     */
    public function endSlot(): void
    {
        foreach(array_reverse($this->slots) as $slot) {
            if(!$slot->isEnded()) {
                $slot->end();
                
                if(!$slot->isReplacing()) {
                    $this->obStack->getCurrent()->writeContent($slot->getMarkup());
                }
                
                return;
            }
        }
        
        throw new RenderException("There are no more slots to end in render context '{$this->obStack->getCurrent()->getName()}'.");
    }
}