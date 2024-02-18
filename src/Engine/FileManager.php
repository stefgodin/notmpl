<?php


namespace StefGodin\NoTmpl\Engine;

/**
 * @internal
 */
class FileManager
{
    public function __construct(
        private readonly array $directories,
        private readonly array $aliases,
        private readonly array $handlers,
    ) {}
    
    /**
     * @param string $name
     * @return string
     * @throws \StefGodin\NoTmpl\Engine\EngineException
     */
    public function resolve(string $name): string
    {
        $filenames = array_unique([
            $this->aliases[$name] ?? $name,
            $name,
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
        
        throw new \StefGodin\NoTmpl\Engine\EngineException(
            sprintf("Could not resolve template file '%s'. Checked for %s", $name, implode(', ', $checkedPaths)),
            \StefGodin\NoTmpl\Engine\EngineException::FILE_NOT_FOUND
        );
    }
    
    public function handle(string $name, array $params): void
    {
        $file = $this->resolve($name);
        
        foreach($this->handlers as $regex => $handler) {
            if(preg_match($regex, $file) !== false) {
                $handler($file, $params);
                return;
            }
        }
    }
}