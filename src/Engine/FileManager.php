<?php
/*
 * This file is part of the NoTMPL package.
 *
 * (c) Stéphane Godin
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */


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
     * @throws EngineException
     */
    public function resolve(string $name): string
    {
        $filenames = array_filter([$this->aliases[$name] ?? null, $name]);
        $directories = [...$this->directories, null];
        $checkedPaths = [];
        foreach($filenames as $filename) {
            foreach($directories as $dir) {
                $file = $checkedPaths[] = ($dir ? $dir . DIRECTORY_SEPARATOR : '') . $filename;
                if(file_exists($file)) {
                    return $file;
                }
            }
        }
        
        throw new EngineException(
            sprintf("Could not resolve file '%s'. Checked for %s", $name, implode(', ', $checkedPaths)),
            EngineException::FILE_NOT_FOUND
        );
    }
    
    /**
     * @param string $name
     * @param array $params
     * @return void
     * @throws EngineException
     */
    public function handle(string $name, array $params): void
    {
        $file = $this->resolve($name);
        
        foreach($this->handlers as $regex => $handler) {
            if(preg_match($regex, $file) !== false) {
                $handler($file, $params);
                return;
            }
        }
        
        throw new EngineException(
            "There are no defined handler for file '{$file}'",
            EngineException::NO_FILE_HANDLER
        );
    }
}