<?php


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 */
class TemplateResolver
{
    public function __construct(
        private readonly array $directories,
        private readonly array $aliases,
    ) {}
    
    /**
     * @param string $template
     * @return string
     * @throws EngineException
     */
    public function resolve(string $template): string
    {
        $filenames = array_unique([
            $this->aliases[$template] ?? $template,
            $template,
        ]);
        
        $checkedPaths = [];
        foreach($filenames as $filename) {
            if(file_exists($filename)) {
                return $filename;
            }
            $checkedPaths[] = '"' . $filename . '"';
            
            foreach($this->directories as $dir) {
                $file = $dir . DIRECTORY_SEPARATOR . $filename;
                if(file_exists($file)) {
                    return $file;
                }
                $checkedPaths[] = '"' . $file . '"';
            }
        }
        
        throw new EngineException(
            sprintf("Could not resolve template file '%s'. Checked for %s", $template, implode(', ', $checkedPaths)),
            EngineException::TMPLRES_FILE_NOT_FOUND
        );
    }
}