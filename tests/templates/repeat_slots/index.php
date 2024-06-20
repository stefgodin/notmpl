<?php

namespace StefGodin\NoTmpl;

/** @formatter:off */
?>
<?php component('product-table') ?>
    <div v-slot="{id, name}"></div>
    <?php foreach(use_repeat_slots('product:name') as ['id' => $id]): ?>
        <?php parent_slot() ?>(<?= $id ?>)
    <?php endforeach ?>
<?php component_end() ?>

<?php component('product-table') ?>

    <?php use_slot('product:name') ?>
        FIRST
    <?php use_slot_end() ?>

    <?php foreach($it = use_repeat_slots('product:name') as $i => $binds): ?>
        <?php parent_slot() ?>(<?= $binds['id'] ?>)
        <?php if($binds['id'] === 3): ?>
            <?php use_slot_end() ?>
            <?php break ?>
        <?php endif ?>
    <?php endforeach ?>
<?php component_end() ?>
