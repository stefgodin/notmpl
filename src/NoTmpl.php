<?php
/*
 * This file is part of the NoTMPL package.
 *
 * (c) Stéphane Godin
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */


namespace StefGodin\NoTmpl;

use StefGodin\NoTmpl\Engine\DefaultFileHandlers;
use StefGodin\NoTmpl\Engine\EngineException;
use StefGodin\NoTmpl\Engine\FileManager;
use StefGodin\NoTmpl\Engine\RenderContext;
use StefGodin\NoTmpl\Engine\RenderContextStack;
use Throwable;

/**
 * This class serves as the main entry point to the NoTMPL rendering engine.
 *
 * It allows for rendering file content with provided parameters, enabling dynamic content generation directly from
 * PHP code.
 */
class NoTmpl
{
    private array $renderGlobalParams;
    private array $directories;
    private array $aliases;
    private array $autoResolvedExtensions;
    private array $fileHandlers;
    
    public function __construct()
    {
        $this->renderGlobalParams = [];
        $this->directories = [];
        $this->aliases = [];
        $this->fileHandlers = [];
        $this->autoResolvedExtensions = ['php'];
    }
    
    /**
     * Renders a file content with given parameters as variables and returns the resulting rendered content as a
     * string.
     *
     * @param string $file The file to render, can be a component alias
     * @param array $parameters The parameters to be passed to the context
     * @return string
     * @throws EngineException
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function render(string $file, array $parameters = []): string
    {
        $fileManager = new FileManager(
            $this->directories,
            $this->aliases,
            $this->autoResolvedExtensions,
            DefaultFileHandlers::merge($this->fileHandlers),
        );
        
        $renderContext = new RenderContext(
            $fileManager,
            $this->renderGlobalParams
        );
        RenderContextStack::$stack[] = $renderContext;
        try {
            $result = $renderContext->render($file, $parameters);
        } catch(Throwable $e) {
            array_pop(RenderContextStack::$stack);
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $e;
        }
        
        array_pop(RenderContextStack::$stack);
        return $result;
    }
    
    /**
     * Sets a global value to be passed into render contexts
     *
     * The 'name' is
     *
     * @param string $name The `$name` of the variable
     * @param mixed $value The value set to the variable
     * @return $this
     */
    public function setRenderGlobalParam(string $name, mixed $value): static
    {
        $this->renderGlobalParams[$name] = $value;
        return $this;
    }
    
    /**
     * Sets multiple global values to be passed into render contexts.
     *
     * @param array $values Key-value pair representing `['name' => value, ...]`
     * @param bool $empty Removes all previously set global values
     * @return $this
     */
    public function setRenderGlobalParams(array $values, bool $empty = false): static
    {
        if($empty) {
            $this->renderGlobalParams = [];
        }
        
        foreach($values as $name => $value) {
            $this->setRenderGlobalParam($name, $value);
        }
        
        return $this;
    }
    
    /**
     * Adds a directory for searching files for render and components
     *
     * Works in conjunction with {@see NoTmpl::setAlias}
     *
     * @param string $directory The directory to search into
     * @return $this
     */
    public function addDirectory(string $directory): static
    {
        if(!in_array($directory, $this->directories)) {
            $this->directories[] = rtrim($directory, '/\\');
        }
        
        return $this;
    }
    
    /**
     * Adds a list of directories to search files for render and components
     *
     * @param array $directories The directories to search into
     * @param bool $empty Removes all previously set directories
     * @return $this
     */
    public function setDirectories(array $directories, bool $empty = false): static
    {
        if($empty) {
            $this->directories = [];
        }
        
        foreach($directories as $directory) {
            $this->addDirectory($directory);
        }
        
        return $this;
    }
    
    /**
     * Sets an alias make loading of specific easier
     *
     * ```php
     * $noTmpl->setAlias('templates/index.php', 'index');
     *
     * $noTmpl->render('index');
     * // or
     * <?php component('index') ?>
     * ```
     *
     *
     * Aliasing also works in conjunction with {@see NoTmpl::addDirectory}
     *
     * The same example could be written like so
     * ```php
     * $noTmpl->addDirectory('templates')
     *        ->setAlias('index.php', 'index');
     *
     * $noTmpl->render('index');
     * ```
     *
     * @param string $file The file to alias
     * @param string $alias The alternative name of the file
     * @return $this
     */
    public function setAlias(string $file, string $alias): static
    {
        $this->aliases[$alias] = $file;
        return $this;
    }
    
    /**
     * Sets many aliases at once
     *
     * @param array $aliases Key-value pair representing `['file' => 'alias']`
     * @param bool $empty Removes all previously set aliases
     * @return $this
     */
    public function setAliases(array $aliases, bool $empty = false): static
    {
        if($empty) {
            $this->aliases = [];
        }
        
        foreach($aliases as $file => $alias) {
            $this->setAlias($file, $alias);
        }
        
        return $this;
    }
    
    /**
     * Adds an extension to be auto-resolved when giving incomplete paths
     *
     * @param string $extension Extension to be auto-resolved
     * @return $this
     */
    public function addAutoResolvedExtensions(string $extension): static
    {
        $this->autoResolvedExtensions[] = $extension;
        $this->autoResolvedExtensions = array_values(array_unique($this->autoResolvedExtensions));
        return $this;
    }
    
    /**
     * Adds multiple extensions to be auto-resolved at once
     *
     * @param array $extensions Extensions to be auto-resolved
     * @param bool $empty Removes all previously set extensions
     * @return $this
     */
    public function setAutoResolvedExtensions(array $extensions, bool $empty = false): static
    {
        if($empty) {
            $this->autoResolvedExtensions = [];
        }
        
        foreach($extensions as $extension) {
            $this->addAutoResolvedExtensions($extension);
        }
        
        return $this;
    }
    
    /**
     * Adds a specific way to load files when file name matches a regex
     *
     * The callable given is used when loading file during render and for components.
     * It will receive the following arguments:
     *  - [0:string]  The file name resolved using configured aliases and directories
     *  - [1:array] A context array with key-value pair representing contextual values merged with configured globals
     *
     * @param string $regex The regex a file name must match
     * @param callable(string, array): void $handler Function that echoes out the content of the file
     * @return $this
     */
    public function addFileHandler(string $regex, callable $handler): static
    {
        $this->fileHandlers[$regex] = $handler;
        return $this;
    }
    
    /**
     * Sets multiple file handlers at once
     *
     * @param array $handlers Key-value pair representing `['/regex/' => callable(...)]`
     * @param bool $empty Removes all added file handlers beforehand
     * @return $this
     */
    public function setFileHandlers(array $handlers, bool $empty = false): static
    {
        if($empty) {
            $this->fileHandlers = [];
        }
        
        foreach($handlers as $regex => $handler) {
            $this->addFileHandler($regex, $handler);
        }
        
        return $this;
    }
}