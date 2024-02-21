# NoTMPL Configuration Reference

This reference provides an overview of the configuration options available in the `NoTmpl` class along with examples of usage.

---

## Global Rendering Parameters

Set global values to be passed into render contexts.

Set a single global value.

```php
$noTmpl->setRenderGlobalParam('site_title', 'My Website');
```

Set multiple global values at once.

```php
$noTmpl->setRenderGlobalParams([
    'site_title' => 'My Website',
    'author' => 'John Doe'
]);
```

Set multiple global values at once and remove old values.

```php
$noTmpl->setRenderGlobalParams([
    'site_title' => 'My Website',
    'author' => 'John Doe'
], true);
```


Globals are then made available as variables when rendering a file (and components) with the engine
```php
// $noTmpl->render('index.php') or component('index.php')
echo $site_title // My Website
echo $author // John Doe
```
---

## File and Directory Management

Configure directories for file searching and manage file aliases.

Add a directory for file searching.

```php
$noTmpl->addDirectory('/path/to/templates');
```

Add multiple directories for file searching.

```php
$noTmpl->setDirectories(['/path/to/templates', '/path/to/other/templates']);
```

Add multiple directories for file searching and remove old ones.

```php
$noTmpl->setDirectories(['/path/to/templates', '/path/to/other/templates'], true);
```

Set an alias for a file.

```php
$noTmpl->setAlias('template.php', 'index');
```

Set multiple aliases at once.

```php
$noTmpl->setAliases(['template.php' => 'index', 'another.php' => 'about']);
```

Set multiple aliases at once and remove old aliases.

```php
$noTmpl->setAliases(['template.php' => 'index', 'another.php' => 'about'], true);
```

Aliases and directories are used to simplify loading of files when using the render function or components.

```php
$noTmpl->render('index');
// Searches in order for:
//     /path/to/templates/template.php
//     /path/to/other/templates/template.php
//     template.php
//     /path/to/templates/index
//     /path/to/other/templates/index
//     index
component('index'); // Does the same
```

---

## Custom File Handling

Configure custom file handlers based on regex patterns to handle file loading in specific ways.

Add a custom file handler for files matching a regex pattern.

```php
$noTmpl->addFileHandler('/\.tmpl$/', function(string $file, array $context) {
    // Custom logic to handle file loading
});
```

Set multiple file handlers at once.

```php
$noTmpl->setFileHandlers(['/\.tmpl$/' => function(string $file, array $context) {
    // Custom logic to handle file loading
}]);
```

Set multiple file handlers at once and remove old ones.

```php
$noTmpl->setFileHandlers(['/\.tmpl$/' => function(string $file, array $context) {
    // Custom logic to handle file loading
}], true);
```

File handlers can be used to load file using special inclusion processing without having to separate the process of 
searching the files and loading specific values into it.

A good example of its usage would be for transforming markdown content into HTML.

```php
$noTmpl->addFileHandler('/\.md$/', function(string $file, array $context) {
    $parser = new \cebe\markdown\Markdown();
    echo $parser->parse(file_get_contents($file));
});

// ...
$noTmpl->render('index.php');

// index.php
component('doc.md')->end() // Loads the file using the markdown handler
```