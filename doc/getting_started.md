# Getting Started with NoTMPL

Welcome to the getting started guide for NoTMPL! This guide will walk you through the process of integrating and using
the NoTMPL library in your PHP projects.

## Installation

To get started with NoTMPL, you'll need to install the library in your project. You can do this via Composer by running
the following command in your terminal:

```bash
composer require stefgodin/notmpl
```

This will download and install the NoTMPL library and its dependencies into your project.

## Basic Usage

Once you've installed NoTMPL, you can start using it to create dynamic pages for your web app. The main concept in
NoTMPL is the component, which represents a reusable piece of markup and PHP code.

### Defining Components

To define a component, simply create a PHP file and write your markup or logic inside it. NoTMPL treats each file as a
component by default, so there's no need to explicitly define it as such.

```php
// templates/header.php
<header>
    <h1>Welcome to our website!</h1>
</header>
```

### Rendering Components

You can render a component using the `render()` method provided by the NoTMPL class. Simply pass the path to the
component file as an argument to the method.

```php
use StefGodin\NoTmpl\NoTmpl;

$noTmpl = new NoTmpl();
echo $noTmpl->render(__DIR__.'/templates/header.php');
```

You can also render a component using the `component()` method within a rendered file.

```php
use StefGodin\NoTmpl\NoTmpl;

$noTmpl = new NoTmpl();
echo $noTmpl->render(__DIR__.'/templates/page.php');
```

```php
// templates/page.php
use function StefGodin\NoTmpl\{component, component_end}; 
?>
<div>
    <?php component(__DIR__.'/header.php') ?>
    <?php component_end() ?>
</div>
<div>This is the page content</div>
```

### Auto-resolving .php

Files names needs no .php extensions by default as they are automatically resolved.

```php
use StefGodin\NoTmpl\NoTmpl;

$noTmpl = new NoTmpl();
echo $noTmpl->render(__DIR__.'/templates/page'/* .php is implicit */);
```

Obviously if a `/templates/page` existed, it would be loaded instead of `templates/page.php`. Check the 
[Advanced Usage](./advanced.md) section to learn more

### Directory Configuration

NoTMPL allows you to configure directories to streamline the referencing of template files. Instead of specifying the
full path to a template file every time you want to render it, you can add directories where your templates reside.

```php
use StefGodin\NoTmpl\NoTmpl;

$noTmpl = new NoTmpl();
$noTmpl->addDirectory(__DIR__.'/templates');
echo $noTmpl->render('page.php');
```

With the directory added, you can simply reference the template file by its name when rendering.

### Aliasing

In addition to directory configuration, NoTMPL supports aliasing to provide shorter and more intuitive names for your
templates. This is particularly useful for frequently used templates that are deeply nested.

```php
$noTmpl->setAlias('path/to/page.php', 'page');
echo $noTmpl->render('page');
```

Now, instead of referencing the template file by its full name, you can use the alias.

### Passing Values to Render

You can pass values from your PHP code to the template files during rendering. This allows for dynamic content
generation based on the context of your application.

```php
$user = new User(id: 1, username: 'stefgodin', email: 'sg@example.com');
echo $noTmpl->render('account.php', ['user' => $user]);
```

In the `account.php` template file, you can access the passed values like `$user`.

This is also true for nested components.

```php
// account.php
?>
<!-- $initials becomes available within the component file -->
<?php component('header', ['initials' => $user->getInitials()]) ?>
<?php component_end() ?>
<div>
    User account
    ...
```

### Using the Default Slot

NoTMPL provides a default slot mechanism for injecting dynamic content into your templates. This allows you to define
placeholders in your template files and fill them with content during rendering.

```php
// templates/footer.php
<footer>
    <p>&copy; <?= date('Y') ?> Basic HTML Layout. All rights reserved.</p>
    <?php slot() ?>
    <?php slot_end() ?>
</footer>
```

In the `page.php` template file, you can fill the default slot with additional content.

```php
// templates/page.php
<?php component('footer') ?>
    <div>Additionnal footer content</div>
<?php component_end() ?>
```

### Using Named Slot

Named slots allow you to inject specific content into predefined areas within a component. They are defined within a
component using the `slot` function with a specified name.

```php
// templates/header.php
<nav>
    <?php slot('menu') ?>
    <?php slot_end() ?>
</nav>
```

They can be filled with content when rendering a component.

```php
// templates/page.php
<?php component('header.php') ?>

    <?php use_slot('menu') ?>
        <div>Additionnal menu content</div> 
    <?php use_slot_end() ?>

<?php component_end() ?>
```

With these basic concepts, you can start building dynamic templates with NoTMPL for your PHP applications. Explore
further to unleash the full potential of NoTMPL's features.

## Next Steps

Now that you've learned the basics of using NoTMPL, you can start building dynamic templates for your web pages. Check
out the [Advanced Usage](./advanced.md) section for more information on how to take full advantage of NoTMPL's features,
and explore the [API Documentation](./api/index.md) for detailed information on all available methods and functions.