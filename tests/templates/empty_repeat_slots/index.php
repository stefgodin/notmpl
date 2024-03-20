<?php

use function StefGodin\NoTmpl\{component, component_end, use_repeat_slots, use_slot, use_slot_end};

/** @formatter:off */
?>

<?php component('component') ?>
    <?php foreach(use_repeat_slots('no_slot') as $binds): ?>
        <div><?= gettype($binds) ?></div>
    <?php endforeach ?>
<?php component_end() ?>

<?php component('component') ?>
    <?php use_slot('one_slot', $binds) ?>
        <div><?= $binds['id'] ?></div>
    <?php use_slot_end() ?>

    <?php foreach(use_repeat_slots('one_slot') as $binds): ?>
        <div><?= $binds['id'] ?></div>
    <?php endforeach ?>
<?php component_end() ?>
