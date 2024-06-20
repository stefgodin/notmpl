# Coding Style and Best Practices

Here are some best practices recommendations to help you and others read and write NoTMPL pages.

As a general rule of thumb, this guide tries to transfer the behavior of HTML tags to the usage of NoTMPL functions.

This guide only contains recommendations, is opinionated and might contradict your personal/company coding rules. Don't
take this guide as a be-all and end-all of using this library or even less of coding in general.

## NoTMPL functions

### Usage

Usage of the NoTMPL (`component/_end`, `slot/_end`, `use_slot/_end`) functions should be done in a single line as much
as possible.

Bad

```php
<?php
component('page', ['title' => 'My page'])
?>
```

Good

```php
<?php component('page', ['title' => 'My page' ]) ?>
```

If a component/slot has too many parameters/bindings to be on a single line, only the array part should be multiline.

```php
<?php component('page', [
    'title' => 'My page',
    ...
]) ?>
```

### Namespace uses

Making the php file part of the `StefGodin\NoTmpl` space is the recommended way to give access to any of the engine 
functions.

```php
<?php

namespace StefGodin\NoTmpl;
```

As an alternative, use the `use function` statement at the top of the page for importing the NoTMPL functions. They 
should be grouped together to decrease the amount of lines used for these functions.

Bad

```php
<?php

use function StefGodin\NoTmpl\component;
use function StefGodin\NoTmpl\component_end;
use function StefGodin\NoTmpl\slot;
use function StefGodin\NoTmpl\slot_end;
use function StefGodin\NoTmpl\use_slot;
use function StefGodin\NoTmpl\parent_slot;
use function StefGodin\NoTmpl\use_slot_end;
use function StefGodin\NoTmpl\esc;
```

Good

```php
<?php

use function StefGodin\NoTmpl\{component,component_end,slot,slot_end,use_slot,parent_slot,use_slot_end,esc};
```


### Indentation

Usage of `component`, `slot`, `use_slot` should increase indentation while usage of their `*_end` counter-part should
reduce the indentation as if their combination was the same as using an HTML tag.

Bad

```php
<?php component('layout') ?>
<div>Indent lvl 0<div>
<?php use_slot('header') ?>
<div>Indent lvl 0<div>
<?php use_slot_end() ?>
    
<?php use_slot('footer') ?>
<?php slot('footer') ?>
<div>Indent lvl 0<div>
<?php slot_end() ?>
<?php use_slot_end() ?>
<?php component_end() ?>
<div>Indent lvl 0<div>
```

Good

```php
<?php component('layout') ?>
    <div>Indent lvl 1<div>
    <?php use_slot('header') ?>
        <div>Indent lvl 2<div>
    <?php use_slot_end() ?>
    
    <?php use_slot('footer') ?>
        <?php slot('footer') ?>
            <div>Indent lvl 3<div>
        <?php slot_end() ?>
    <?php use_slot_end() ?>
<?php component_end() ?>
<div>Indent lvl 0<div>
```

### Autoformatting issues

If you have autoformat enabled, most IDE autoformatting can be controlled using the `@formatted:off` annotation

```php
<?php

use function ...

/** @formatter:off */
```

If to enable again it within a page, most IDE will also accept the `@formatted:on` annotation

```php
<?php

use function ...

/** @formatter:on */
```

## Variables

Expected variables should be documented within the top of the php file.

```php
<?php

use function ...

/**
 * @var string $var
 * @var MyClass $myClassVar
 */
```

### Default values

If variables are optional

```php
<?php

/**
 * @var string $var <-- Document it to indicate its an expected value
 * @var MyClass $myClassVar
 */

$var ??= 'Default value';
```

### Definition/manipulation within the page

Defining or acting upon a variable within the page should be done in one line as much as possible and be between
`<?php ... ?>` tags.

```php
...
?>
<?php component('page') ?>
    <?php $value = 'hello' ?>
    ...
<?php component_end() ?>
```

Larger values, for example large arrays and anonymous functions, should be declared at the top of the page if possible.

### Output

Outputting a variable within a template should be done in a single line using the short echo syntax `<?= ... ?>`.

Bad

```php
<?php echo $var ?>
<div attr="<?php echo $var ?>"><?php echo $var ?></div> 
```

Good

```php
<?= $var ?>
<div attr="<?= $var ?>"><?= $var ?></div>
```

> Note: Be careful when outputting variables to the template as they are not escaped by default. Refer to the
> [Escaping Guide](./Escaping.md) for more information on the subject.

## Control flow statements (foreach, if, ...)

We recommend using [alternative syntax](https://www.php.net/manual/en/control-structures.alternative-syntax.php) of
control statements. Most statements should be in a single line between `<?php ... ?>` tags.

### If, elseif, else

```php
<?php if(...): ?>
    ...
<?php elseif(...): ?>
    ...
<?php else: ?>
    ...
<?php endif ?>
```

### Foreach

```php
<?php foreach(... as ...): ?>
    ...
<?php endforeach ?>
```

### For

```php
<?php for (...; ...; ...): ?>
    ...
<?php endfor ?>
```

### While

```php
<?php while (...): ?>
    ...
<?php endwhile ?>
```

### Do-while

Do-while loops don't have an alternative syntax. So you'll have to use the normal one.

```php
<?php do { ?>
    ...
<?php } while (...) ?>
```

### Switch cases

```php
<?php switch (...): ?>
<?php case ...: ?>
    ...
    <?php break ?>
<?php case ...: ?>
    ...
    <?php break ?>
<?php endswitch; ?>
```

> Note: Switch statements have a weird quirk requiring the `case` to be at the same indent level as the `switch`