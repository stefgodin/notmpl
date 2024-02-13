<?php

use function StefGodin\NoTmpl\Render\component;
use function StefGodin\NoTmpl\Render\component_end;
use function StefGodin\NoTmpl\Render\parent_slot;
use function StefGodin\NoTmpl\Render\slot;
use function StefGodin\NoTmpl\Render\slot_end;

/**
 * @var string[] $items
 */

/** @formatter:off **/
?>
<div>
    <?php component('menu') ?>
    
        <?php slot('title') ?>
            <?php parent_slot() ?>
            <?php component('menu-title')->end() ?>
        <?php slot_end() ?>
    
        <?php slot('items') ?>
            <?php foreach($items as $item): ?>
                <?php component('menu-item', ['text' => $item])->end() ?>
            <?php endforeach ?>
        <?php slot_end() ?>
    
    <?php component_end() ?>
</div>