<?php
/** @noinspection PhpArrayAccessOnIllegalTypeInspection */

use function StefGodin\NoTmpl\{component, component_end, use_slot, use_slot_end};

$binds = 1;

/** @formatter:off */
?>
<?php component('component.php') ?>
    <?php use_slot(bindings: $binds) ?>
        <div><?= $binds['head'] ?></div>
        <?php component('subcomponent.php') ?>
            <?php use_slot(bindings: $binds) ?>
                <div><?= $binds['head'] ?></div>
                <div><?= $binds['foot'] ?></div>
            <?php use_slot_end() ?>
        <?php component_end() ?>
        <div><?= $binds['foot'] ?></div>
    <?php use_slot_end() ?>
<?php component_end() ?>
<div><?= $binds ?></div>

<?php component('component.php') ?>
    <?php use_slot(bindings: $otherBinds) ?>
        <div><?= $otherBinds['head'] ?></div>
        <?php component('subcomponent.php') ?>
            <?php use_slot(bindings: $otherBinds) ?>
                <div><?= $otherBinds['head'] ?></div>
                <div><?= $otherBinds['foot'] ?></div>
            <?php use_slot_end() ?>
        <?php component_end() ?>
        <div><?= $otherBinds['foot'] ?></div>
    <?php use_slot_end() ?>
<?php component_end() ?>
<div><?= gettype($otherBinds) ?></div>