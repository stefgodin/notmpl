# Building an example

The main aspect of a rendering engine is to allow for some sort of template composition. The NoTMPL uses a simple
composition API made of a few namespaced functions. These allow for most extensibility needs within a template engine.

## Any file is a component

You don't have to define a file as a component, it is perceived as one by default.

Here is a `templates/site_layout.php` representing the base layout of our application.

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My app title</title>
    <link red>
</head>
<body>
    <header>
        <h1>My app title</h1>
    </header>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </nav>
    <section>
        <h2>Welcome to our website!</h2>
    </section>
    <footer>
        <p>&copy; 2024 Basic HTML Layout. All rights reserved.</p>
    </footer>
</body>
</html>
```

Now let's say we have our first page we want to create as `templates/account.php`.

```php
use function StefGodin\NoTmpl\{component, component_end, slot, slot_end};

?>
<?php component(__DIR__.'/site_layout.php') ?>
<?php component_end() ?>
```

Now we can use it in our `public/index.php` file like so

```php
use StefGodin\NoTmpl\NoTmpl;

$noTmpl = new NoTmpl();
echo $noTmpl->render(__DIR__.'/../templates/account.php'); // The whole unmodified layout will echo out
```

## Directories and aliasing

This is all fine but writing every complete path is quite a task. This is when directory configuration comes into play.\
In `public/index.php`
```php
use StefGodin\NoTmpl\NoTmpl;

$noTmpl = new NoTmpl();
$noTmpl->addDirectory(__DIR__.'/../templates'); // <--

echo $noTmpl->render('account.php'); // Already better
```

Now in `templates/account.php`
```php
use function StefGodin\NoTmpl\{component, component_end};

?>
<?php component('site_layout.php') ?>
<?php component_end() ?>
```

As we know, `site_layout.php` will be used by a lot of pages, so we might want a shorter alias for it.\
In `public/index.php`
```php
use StefGodin\NoTmpl\NoTmpl;

$noTmpl = new NoTmpl();
$noTmpl->addDirectory(__DIR__.'/../templates')
    ->setAlias('site_layout.php', 'layout'); // <--

echo $noTmpl->render('account.php');
```

Now in `account.php`
```php
use function StefGodin\NoTmpl\{component, component_end};

?>
<?php component('layout') ?>
<?php component_end() ?>
```

## Passing values
We can pass values from pages to components using the `component` second argument.\
Let's send our page name to change the page title.

In `account.php`
```php
...
<?php component('layout', ['title' => 'My Account']) ?>
<?php component_end() ?>
```

And now use it in our `layout` component
```php
/**
 * @var string $title <-- This will help your IDE
 */
$title ??= 'My app title';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link red>
</head>
<body>
    <header>
        <h1><?= $title ?></h1>
    </header>
...
```

> Security Warning!\
> Using variables that are not controlled by you may contain user added HTML and will make your app vulnerable to 
> [XSS attacks](https://owasp.org/www-community/attacks/xss/). You must escape unsafe values yourself by using the 
> available `esc_html` function or by using a well tested library such as 
> [laminas-escaper](https://packagist.org/packages/laminas/laminas-escaper). 

Now we may want to send the current username to `account.php`.
In `public/index.php`
```php
...

echo $noTmpl->render('account.php', [
    'user' => new User(id: 1, username: 'stefgodin', email: 'sg@example.com'),
]);
```

In `account.php`
```php
use function StefGodin\NoTmpl\{component, component_end};

/**
 * @var User $user 
 */
?>
<?php component('layout', ['title' => "{$user->getUserName()} Account"]) ?>
<?php component_end() ?>
```

Let's make our `My app title` and `en` from `lang="en"` into a variables.\
It wouldn't be nice to have to pass them every time we use `templates/site_layout.php`, so let's make them globally
available.

In `public/index.php`
```php
use StefGodin\NoTmpl\NoTmpl;

$noTmpl = new NoTmpl();
$noTmpl->addDirectory(__DIR__.'/../templates')
    ->setAlias('site_layout.php', 'layout')
    ->setRenderGlobalParams([
        'lang' => "en",
        'app' => "My app title"
    ]);


echo $noTmpl->render('account.php', [
    'user' => new User(id: 1, username: 'stefgodin', email: 'sg@example.com'),
]);
```

And now in our `layout` component
```php
/**
 * @var string $lang
 * @var string $app
 * @var string $title
 */
$title ??= $app;
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link red>
</head>
<body>
    <header>
        <h1><?= $title ?></h1>
    </header>
...
```

Well now we got some dynamic values. Once again it ain't much but at least we're starting to gain some value from using 
a library.

## Adding slots

At this point, we would like to inject some content that would awkwardly fit in a variable such as some HTML fragment.\
Let's say we want our account page to show some user info.

First we start by declaring a slot within our `layout` component file.
```php
use function StefGodin\NoTmpl\{slot, slot_end};
...
?>
...
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </nav>
    <section>
        <?php slot() ?>
        <?php slot_end() ?>
    </section>
    <footer>
        <p>&copy; 2024 Basic HTML Layout. All rights reserved.</p>
    </footer>
</body>
</html>
```

Then we use it in `account.php`
```php
use function StefGodin\NoTmpl\{component, component_end};

/**
 * @var User $user 
 */
?>
<?php component('layout', ['title' => "{$user->getUserName()} Account"]) ?>
    <div>
        <strong>UserName :</strong>
        <span><?= $user->getUserName() ?></span>
    </div>
    <div>
        <strong>Email :</strong>
        <span><?= $user->getEmail() ?></span>
    </div>
<?php component_end() ?>
```

This will do the same as writing in `layout`
```php
?>
...
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </nav>
    <section>
        <div>
            <strong>UserName :</strong>
            <span>stefgodin</span>
        </div>
        <div>
            <strong>Email :</strong>
            <span>sg@example.com</span>
        </div>
    </section>
    <footer>
        <p>&copy; 2024 Basic HTML Layout. All rights reserved.</p>
    </footer>
</body>
</html>
```

### Named slots
That is quite convenient, but we might want to also be able to change the footer without having to wrap it and the
section into a single slot.

That's when we want to use names on our slots.

Let's make a slot for the footer in our `layout` component
```php
?>
...
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </nav>
    <section>
        <?php slot() ?>
        <?php slot_end() ?>
    </section>
    <footer>
        <p>&copy; 2024 Basic HTML Layout. All rights reserved.</p>
        <?php slot('footer') ?>
        <?php slot_end() ?>        
    </footer>
</body>
</html>
```

And now we can also use it in our `account.php`
```php
use function StefGodin\NoTmpl\{component, component_end, use_slot, use_slot_end};

/**
 * @var User $user 
 */
?>
<?php component('layout', ['title' => "{$user->getUserName()} Account"]) ?>
    <div>
        <strong>UserName :</strong>
        <span><?= $user->getUserName() ?></span>
    </div>
    <div>
        <strong>Email :</strong>
        <span><?= $user->getEmail() ?></span>
    </div>
    
    <?php use_slot('footer') ?>
        <div>Check out our information <a href="/policy">policy</a></div>
        <div>Ask for support <a href="/support?user=<?= $user->getId() ?>">support</a></div>
    <?php use_slot_end() ?>
<?php component_end() ?>
```

### Slots default content
Great! But now maybe the policy link should be there as a default when we don't want to overwrite the footer.\
Let's put it into the `layout` component footer slot
```php
?>
...
    <footer>
        <p>&copy; 2024 Basic HTML Layout. All rights reserved.</p>
        <?php slot('footer') ?>
            <div>Check out our information <a href="/policy">policy</a></div>
        <?php slot_end() ?>        
    </footer>
...
```

And now we remove it from `account.php`
```php
...
?>
<?php component('layout', ['title' => "{$user->getUserName()} Account"]) ?>
    <div>
        <strong>UserName :</strong>
        <span><?= $user->getUserName() ?></span>
    </div>
    <div>
        <strong>Email :</strong>
        <span><?= $user->getEmail() ?></span>
    </div>
    
    <?php use_slot('footer') ?>
        <!-- Removed -->
        <div>Ask for support <a href="/support?user=<?= $user->getId() ?>">support</a></div>
    <?php use_slot_end() ?>
<?php component_end() ?>
```

Now the link to information policy will be shown when we don't use the footer slot.

### Using parent slot

But now only the link for support is shown in `account.php`. Let's bring it back using the `parent_slot` function.
```php
use function StefGodin\NoTmpl\{component, component_end, use_slot, use_slot_end, parent_slot};

/**
 * @var User $user 
 */
?>
<?php component('layout', ['title' => "{$user->getUserName()} Account"]) ?>
    <div>
        <strong>UserName :</strong>
        <span><?= $user->getUserName() ?></span>
    </div>
    <div>
        <strong>Email :</strong>
        <span><?= $user->getEmail() ?></span>
    </div>
    
    <?php use_slot('footer') ?>
        <?php parent_slot() // <---- ?>
        <div>Ask for support <a href="/support?user=<?= $user->getId() ?>">support</a></div>
    <?php use_slot_end() ?>
<?php component_end() ?>
```

This will render the used slot default content where we use the `parent_slot` function. In our case, the information 
policy link.

### Any slot is a named slot
This might seem weird right now, the content put directly within the `component` and `component_end` functions is used
in the `slot` without a name, but the other named slot requires `use_slot` and `use_slot_end` calls...

Well, it is a bit weird, but the truth is that declaring a `slot` without a name actually sets that slot's name to 
`"default"` and the usage of `component` also start an implicit use of `default` named slot.

The whole example could be rewritten as such.

In `layout`
```php
?>
...
    <section>
        <?php slot('default') ?>
        <?php slot_end() ?>
    </section>
    <footer>
        <p>&copy; 2024 Basic HTML Layout. All rights reserved.</p>
        <?php slot('footer') ?>
            <div>Check out our information <a href="/policy">policy</a></div>
        <?php slot_end() ?>        
    </footer>
</body>
</html>
```

In `account.php`
```php
use function StefGodin\NoTmpl\{component, component_end, use_slot, use_slot_end, parent_slot};

/**
 * @var User $user 
 */
?>
<?php component('layout', ['title' => "{$user->getUserName()} Account"]) ?>
    <?php use_slot('default') ?>
        <div>
           <strong>UserName :</strong>
           <span><?= $user->getUserName() ?></span>
        </div>
        <div>
           <strong>Email :</strong>
           <span><?= $user->getEmail() ?></span>
        </div>
    <?php use_slot_end() ?>
    
    <?php use_slot('footer') ?>
        <?php parent_slot() ?>
        <div>Ask for support <a href="/support?user=<?= $user->getId() ?>">support</a></div>
    <?php use_slot_end() ?>
<?php component_end() ?>
```

> Side Note: The order in which you choose to use slots does not matter

### Slot value binding
Sometimes we may work on some values within a component file and want it to be available where we use the component.

For example, in `layout` component's footer we have a dynamic css class for the content that changes the footer's elements
style depending on some value. We could write the logic to generate that class into a separate service and inject it 
globally, but since it's only related to the footer, it would only clutter our application.

Let's bind it directly to our footer slot in `layout` instead
```php
...

$dynamicClass = ['small-font', 'large-font'][random_int(0, 1)]; // Loads a random font size for the footer
?>
...
    <section>
        <?php slot() ?>
        <?php slot_end() ?>
    </section>
    <footer>
        <p>&copy; 2024 Basic HTML Layout. All rights reserved.</p>
        <?php slot('footer', ['class' => $dynamicClass]) ?>
            <div class="<?= $dynamicClass ?>">Check out our information <a href="/policy">policy</a></div>
        <?php slot_end() ?>        
    </footer>
</body>
</html>
```

And now use it in `account.php`
```php
use function StefGodin\NoTmpl\{component, component_end, use_slot, use_slot_end, parent_slot};

/**
 * @var User $user 
 */
?>
<?php component('layout', ['title' => "{$user->getUserName()} Account"]) ?>
    <div>
       <strong>UserName :</strong>
       <span><?= $user->getUserName() ?></span>
    </div>
    <div>
       <strong>Email :</strong>
       <span><?= $user->getEmail() ?></span>
    </div>
    
    <?php use_slot('footer', $binds) // The variable does not need to exist beforehand ?>
        <?php parent_slot() ?>
        <div class="<?= $binds['class'] ?>">Ask for support <a href="/support?user=<?= $user->getId() ?>">support</a></div>
    <?php use_slot_end() ?>
<?php component_end() ?>
```

> Side Note: Value bindings can be of any type, but be careful. Bindings cannot alter the component's content since it's
> already rendered at that point. In this case changing the class `$binds['class'] = "something-else"` wouldn't alter
> the component footer slot.

> Side Note: Values can be bound to the default slot, but they require calling `use_slot` in order to become accessible.

### Short syntax
We have seen that declaring `slot` requires a `slot_end` call, but it is not exact as every `component`, `slot` and 
`use_slot` returns an object allowing for a direct `->end()` method removing the need to call a subsequent `*_end` 
function.

It is most useful in these scenarios:
 - `component([...])->end()`: Using a component without any of its slots
 - `slot([...])->end()`: Declaring a slot without any default content
 - `use_slot([...])->end()`: Hiding a slot content

Let's change our default slot from `layout` since it has no default content.
```php
?>
...
    <section>
        <?php slot()->end() ?>
    </section>
    <footer>
        <p>&copy; 2024 Basic HTML Layout. All rights reserved.</p>
        <?php slot('footer', ['class' => $dynamicClass]) ?>
            <div class="<?= $dynamicClass ?>">Check out our information <a href="/policy">policy</a></div>
        <?php slot_end() ?>        
    </footer>
</body>
</html>
```

## Multiple components and nesting
Now it wouldn't be a composition based engine if we could only use one component at a time.

We can indeed nest components into one another and combine slots for very deep and advanced usages.

Let's add a `nav.php` component in `templates/` directory

```php
use function StefGodin\NoTmpl\slot

// Gets a class according to a specific link state (ex: active/inactive)
$get_link_class = function(string $path): string {
    // ...
}

/**
 * @var string[] $links
 */
?>
<nav>
    <ul>
        <?php slot('prepend-header', ['get_active_class' => $get_link_class])->end() ?>
        <?php foreach($links as $link => $text): ?>
             <li class="<?= $get_link_class($link) ?>"><a href="<?= $link ?>"><?= $text ?></a></li>
        <?php endforeach ?>
        <?php slot('append-header', ['get_active_class' => $get_link_class])->end() ?>
    </ul>
</nav>
```

Now we use it in `layout` 
```php
use function StefGodin\NoTmpl\{component, component_end, slot, slot_end, use_slot, use_slot_end, parent_slot};

...

$links = [
    '/' => 'Home',
    '/about' => 'About',
    '/contact' => 'Contact',
]
?>
...
    <header>
        <h1><?= $title ?></h1>
    </header>
    <?php component('nav.php', ['links' => $links]) ?>
    
        <?php use_slot('append-header', $binds) ?>
            <li class="<?= $binds['get_link_class']('/account') ?>"><a>Account</a></li>
            <?php slot('append-links', $binds)->end() ?>
        <?php use_slot_end ?>
        
    <?php component_end() ?>
    <section>
        <?php slot()->end() ?>
    </section>
...
```

And we can now also add links from `account.php` 
```php
use function StefGodin\NoTmpl\{component, component_end, use_slot, use_slot_end, parent_slot};

/**
 * @var User $user 
 */
?>
<?php component('layout', ['title' => "{$user->getUserName()} Account"]) ?>
    
    <?php use_slot('append-links', $binds) ?>
        <li class="<?= $binds['get_link_class']('/settings') ?>"><a>Settings</a></li>
    <?php use_slot_end() ?>

    <div>
       <strong>UserName :</strong>
       <span><?= $user->getUserName() ?></span>
    </div>
    <div>
       <strong>Email :</strong>
       <span><?= $user->getEmail() ?></span>
    </div>
    
    <?php use_slot('footer', $binds) ?>
        <?php parent_slot() ?>
        <div class="<?= $binds['class'] ?>">Ask for support <a href="/support?user=<?= $user->getId() ?>">support</a></div>
    <?php use_slot_end() ?>
<?php component_end() ?>
```

It is also possible to compose a page using multiple components side-by-side or even nest component in slots, slots 
in components, slots in slots, components in components, slots. 

The only thing you can't do is call a `use_slot` outside of a `component` call.

## Final result

Let's see every file we've created one last time.

`templates/site_layout.php`
```php
<?php

use function StefGodin\NoTmpl\{component, component_end, slot, slot_end, use_slot, use_slot_end, parent_slot};

/**
 * @var string $lang
 * @var string $app
 * @var string $title
 */

$title ??= $app;

$links = [
    '/' => 'Home',
    '/about' => 'About',
    '/contact' => 'Contact',
]
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link red>
</head>
<body>
    <header>
        <h1><?= $title ?></h1>
    </header>
    <?php component('nav.php', ['links' => $links]) ?>
        <?php use_slot('append-header', $binds) ?>
            <li class="<?= $binds['get_link_class']('/account') ?>"><a>Account</a></li>
            <?php slot('append-links', $binds)->end() ?>
        <?php use_slot_end ?>
    <?php component_end() ?>
    <section>
        <?php slot()->end() ?>
    </section>
    <footer>
        <p>&copy; 2024 Basic HTML Layout. All rights reserved.</p>
        <?php slot('footer', ['class' => $dynamicClass]) ?>
            <div class="<?= $dynamicClass ?>">Check out our information <a href="/policy">policy</a></div>
        <?php slot_end() ?>        
    </footer>
</body>
</html>
```

`templates/nav.php`
```php
<?php

use function StefGodin\NoTmpl\slot

// Gets a class according to a specific link state (ex: active/inactive)
$get_link_class = function(string $path): string {
    // returns 'active' or 'inactive'
}

/**
 * @var string[] $links
 */
?>
<nav>
    <ul>
        <?php slot('prepend-header', ['get_active_class' => $get_link_class])->end() ?>
        <?php foreach($links as $link => $text): ?>
             <li class="<?= $get_link_class($link) ?>"><a href="<?= $link ?>"><?= $text ?></a></li>
        <?php endforeach ?>
        <?php slot('append-header', ['get_active_class' => $get_link_class])->end() ?>
    </ul>
</nav>
```

`templates/account.php`
```php
<?php

use function StefGodin\NoTmpl\{component, component_end, use_slot, use_slot_end, parent_slot};

/**
 * @var User $user 
 */
?>
<?php component('layout', ['title' => "{$user->getUserName()} Account"]) ?>
    
    <?php use_slot('append-links', $binds) ?>
        <li class="<?= $binds['get_link_class']('/settings') ?>"><a href="/settings">Settings</a></li>
    <?php use_slot_end() ?>

    <div>
       <strong>UserName :</strong>
       <span><?= $user->getUserName() ?></span>
    </div>
    <div>
       <strong>Email :</strong>
       <span><?= $user->getEmail() ?></span>
    </div>
    
    <?php use_slot('footer', $binds) ?>
        <?php parent_slot() ?>
        <div class="<?= $binds['class'] ?>">Ask for support <a href="/support?user=<?= $user->getId() ?>">support</a></div>
    <?php use_slot_end() ?>
<?php component_end() ?>
```

`public/index.php`
```php
<?php

use StefGodin\NoTmpl\NoTmpl;

$noTmpl = new NoTmpl();
$noTmpl->addDirectory(__DIR__.'/../templates')
    ->setAlias('site_layout.php', 'layout')
    ->setRenderGlobalParams([
        'lang' => "en",
        'app' => "My app title"
    ]);


echo $noTmpl->render('account.php', [
    'user' => new User(id: 1, username: 'stefgodin', email: 'sg@example.com'),
]);
```

And now the final output would be
```html
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>stefgodin Account</title>
    <link red>
  </head>
  <body>
    <header>
      <h1>stefgodin Account</h1>
    </header>
    <nav>
      <ul>
        <li class="inactive"><a href="/">Home</a></li>
        <li class="inactive"><a href="/about">About</a></li>
        <li class="inactive"><a href="/contact">Contact</a></li>
        <li class="inactive"><a href="/contact">Contact</a></li>
        <li class="active"><a href="/account">Account</a></li>
        <li class="inactive"><a href="/settings">Settings</a></li>
      </ul>
    </nav>
    <section>
      <div>
        <strong>UserName :</strong>
        <span>stefgodin</span>
      </div>
      <div>
        <strong>Email :</strong>
        <span>sg@example.com</span>
      </div>
    </section>
    <footer>
      <p>&copy; 2024 Basic HTML Layout. All rights reserved.</p>
      <div class="small-font">Check out our information <a href="/policy">policy</a></div>
      <div class="small-font">Ask for support <a href="/support?user=1">support</a></div>
    </footer>
  </body>
</html>
```

Hoping this is enough to get you to understand most of what this engine can do. The rest can be found in great detail in
the [documentation](./index.md).