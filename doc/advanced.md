# Advanced Usage

This guide aims to explain some advanced usage of the NoTMPL library. If you are new, you should check out the 
[Getting Started Guide](./getting_started.md).

## Short slot, component and use_slot
Using `component`, `slot` and `use_slot` usually requires a `*_end` call to end its context. If there is no content to 
add within the element it can be ended immediately using the `->end()` method.

```php
<?php component('...')->end() ?>

<?php slot('...')->end() ?>

<?php component('...') ?>
    <?php use_slot('...')->end() ?>
<?php component_end() ?>
```

It is most useful in these scenarios:
- `component([...])->end()`: Using a component without any of its slots
- `slot([...])->end()`: Declaring a slot without any default content
- `use_slot([...])->end()`: Hiding a slot content

## Slot bindings

Sometimes, it's required to allow passing values from within the components `slot` to their respective `use_slot`. This
is something easily achievable using the second `slot` and `use_slot` parameters.

```php
// my_component.php
<?php slot('the_slot', ['username' => 'johndoe', 'age' => 44])->end() ?>
```

```php
// index.php
<?php component('my_component') ?>
    <?php use_slot('the_slot', $binds) ?>
        <?= $binds['username'] // johndoe ?>
        <?= $binds['age'] > 21 // true ?>
    <?php use_slot_end() ?>
<?php component_end() ?>
```

When we set a variable on the second parameter of `use_slot`, the variable is passed by reference. It doesn't need to
exist beforehand and will be reset to its original value once out of the `use_slot` context.

For example:

```php
// index.php
$binds = 1;
?>
<?= $binds // 1 ?>

<?php component('my_component') ?>
    <?php use_slot('the_slot', $binds) ?>
        <?= $binds['username'] // johndoe ?>
    <?php use_slot_end() ?>
<?php component_end() ?>

<?= $binds // 1 ?>
```

It allows you to reuse the same variable for many `use_slot`

## Dynamic slots

Sometimes we want a component to have some logic that will dynamically define its slots.

```php
// my_component.php
<?php if($check /* false */): ?>
    <?php slot('my_slot', ['id' => 'Some content']) ?>
        <div>Some content</div> 
    <?php slot_end() ?>
<?php endif ?>
```

This may cause issues on the user side as using `use_slot` does not prevent the code under it from being executed.

```php
// index.php
<?php component('my_component') ?>
    <?php use_slot('my_slot', $binds) ?>
        <div>Overwritting <?= $binds['id'] ?></div> <!-- Undefined index 'id' ... -->
    <?php use_slot_end() ?>
<?php component_end() ?>
```

To prevent this issue, the `has_slot` function can be used to check if a slot of a given name was not yet used for a
component.


```php
// index.php
<?php component('my_component') ?>
    <?php if(has_slot('my_slot')): ?>
        <!-- skipped -->
        <?php use_slot('my_slot', $binds) ?>
            <div>Overwritting <?= $binds['id'] ?></div>
        <?php use_slot_end() ?>
    <?php endif ?>
<?php component_end() ?>
```

## Slots within a loop

Let's start with the basics. Multiple `slot` can have the same name. They will require the same number of `use_slot` to 
overwrite each of them.

```php
// my_component.php
<?php slot('my_slot', ['id' => 'Some content']) ?>
    <div>Some content</div> 
<?php slot_end() ?>


<?php slot('my_slot', ['id' = 'Some other content']) ?>
    <div>Some other content</div> 
<?php slot_end() ?>
```

```php
// index.php
<?php component('my_component') ?>
    <?php use_slot('my_slot', $binds) ?>
        <div>Overwritting <?= $binds['id'] ?></div>
    <?php use_slot_end() ?>
    

    <?php use_slot('my_slot', $binds) ?>
        <div>Overwritting <?= $binds['id'] ?></div>
    <?php use_slot_end() ?>
<?php component_end() ?>
```

Will result as

```html
<div>Overwritting Some content</div>
<div>Overwritting Some other content</div>
```

Knowing this, we can conclude that it can be used within loops.

Here is the same example but with foreach loops instead.

```php
// my_component.php
<?php foreach(['Some content', 'Some other content'] as $content): ?>
    <?php slot('my_slot', ['id' => $content]) ?>
        <div><?= $content ?></div> 
    <?php slot_end() ?>
<?php endforeach ?>
```

```php
// index.php
<?php component('my_component') ?>
    <?php foreach([0, 1] as $i): ?>
        <?php use_slot('my_slot', $binds) ?>
            <div><?= $binds['id'] ?></div>
        <?php use_slot_end() ?>
    <?php endforeach ?>
<?php component_end() ?>
```

Now, in this example, `index.php` knows the number of slots looped over. But in a more standard scenario it might not be
the case. 

Let's imagine we have a product table component, and we want the component user to be able to change the "action" column
by using `slot` based on the current product row.

```php
// products.php

// Normally you would load your products from a DB
$products = [
    new Product(id: 1, name: 'Oreo', price: 500),
    new Product(id: 2, name: 'Potato', price: 799),
    new Product(id: 3, name: 'BBQ Chips', price: 499),
    new Product(id: 4, name: 'Kit Kat', price: 255),
];

?>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th><!-- Actions --></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($products as $product): ?>
            <tr>
                <td><?= $product->name ?></td>
                <td><?= format_price($product->price) ?></td>
                <td><?php slot('action', ['product' => $product])->end() ?></td> <!-- Here the slot is created -->
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
```

Now if we try the same pattern as before we will have a problem since we don't have access to the `$products` array or 
its `count($products)` to allow our iteration.

```php
// index.php
<?php component('products') ?>
    <?php foreach([/* we don't have access to the number of products here */] as $i): ?>
        <?php use_slot('action', $binds) ?>
            <div><button product="<?= $binds['product']['id'] ?>">Edit</button></div>
        <?php use_slot_end() ?>
    <?php endforeach ?>
<?php component_end() ?>
```

This is when `use_repeat_slots` finds its use.

```php
// index.php
<?php component('products') ?>
    <?php foreach(use_repeat_slots('action') as $binds): ?>
        <div><button product="<?= $binds['product']['id'] ?>">Edit</button></div>
    <?php endforeach ?>
<?php component_end() ?>
```

`use_repeat_slots` does 3 nice things:
1. Automatically loops on the number of slots declared within the component with the specified name
2. Starts and ends a `use_slot` on each iteration, so you don't have to
3. Gives access to the slot `bindings` from the iterated value

> Note: The slot iterated on does not need to be repeated within the component for `use_repeat_slots` to work. In fact, 
> it does not even need to exist.

> Note 2: Breaking out of the foreach early using `break` will leave an open `use_slot`. There are very few reasons to 
> do so. Most of the time, using `parent_slot` does the wanted job. In other cases, you should use the `use_slot_end`
> function before the `break` to prevent this issue.