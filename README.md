# NoTMPL
A light-weight template-less rendering engine for PHP.

## Specs
 - [x] No dependencies
 - [x] Lightweight code base (< 1000 LOC)
 - [x] Made for back-end devs
 - [x] No cache directory
 - [x] No eval

What it won't do
 - [ ] Autoescape variables
 - [ ] Sandbox templates

```php
<?php // public/index.php

use StefGodin\NoTmpl\NoTmpl;

require_once __DIR__.'/../vendor/autoload.php'
    
NoTmpl::config()
    ->addTemplateDirectory(__DIR__.'/../templates')
    ->setTemplateAlias('my_page_component.php', 'page_layout')
    
$result = NoTmpl::render('main.php', ['titleValue' => 'My custom title']);
```

```php
<?php // templates/main.php

use function StefGodin\NoTmpl\component;
use function StefGodin\NoTmpl\component_end;
use function StefGodin\NoTmpl\use_slot;
use function StefGodin\NoTmpl\use_slot_end;
use function StefGodin\NoTmpl\esc_html;

/**
 * @var string $titleValue 
 */
?>
<?php component('page_layout') ?>
    <?php use_slot('title') ?>
        <h1><?= esc_html($titleValue) ?></h1>
    <?php use_slot_end() ?>
    
    <div>
        <h2>My content</h2>
    </div>
<?php component_end() ?>

<div>A footer</div>
```

```php
<?php // templates/my_page_component.php

use function StefGodin\NoTmpl\slot;
use function StefGodin\NoTmpl\slot_end;

?>
<div>A header</div>

<?php slot('title') ?>
    <h1>My normal title</h1>
<?php slot_end() ?>
<div>
    <?php slot() ?>
        <div>Some default content</div>
    <?php slot_end() ?>
</div>
```

## Requirements
This library requires PHP 8.1+

## Installation
Install the library using composer:
```
composer require stefgodin/notmpl
```

## Documentation
Learn by reading the [documentation](doc/index.md).

## Support
 - [Issues](https://github.com/stefgodin/notmpl/issues)