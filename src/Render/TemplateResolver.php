<?php


namespace Stefmachine\NoTmpl\Render;

use Stefmachine\NoTmpl\Config\ConfigInjectTrait;
use Stefmachine\NoTmpl\Exception\RenderException;
use Stefmachine\NoTmpl\Singleton\SingletonTrait;

/**
 * @internal
 */
class TemplateResolver
{
    use SingletonTrait;
    use ConfigInjectTrait;
    
    /**
     * @param string $template
     * @return string
     * @throws RenderException
     */
    public function resolve(string $template): string
    {
        $filenames = array_unique([
            $this->getConfig()->getTemplateAliases()[$template] ?? $template,
            $template,
        ]);
        
        $checkedPaths = [];
        foreach($filenames as $filename) {
            if(file_exists($filename)) {
                return $filename;
            }
            $checkedPaths[] = '"' . $filename . '"';
            
            foreach($this->getConfig()->getTemplateDirectories() as $dir) {
                $file = $dir . DIRECTORY_SEPARATOR . $filename;
                if(file_exists($file)) {
                    return $file;
                }
                $checkedPaths[] = '"' . $file . '"';
            }
        }
        
        throw new RenderException(sprintf("Could not resolve template file '%s'. Checked for %s", $template, implode(', ', $checkedPaths)));
    }
}