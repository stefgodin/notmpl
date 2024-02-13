<?php

use function StefGodin\NoTmpl\Render\component;
use function StefGodin\NoTmpl\Render\component_end;
use function StefGodin\NoTmpl\Render\parent_slot;
use function StefGodin\NoTmpl\Render\use_slot;
use function StefGodin\NoTmpl\Render\use_slot_end;

/** @formatter:off */
?>

<div>index_header</div>
<div>
    <?php component('page') ?>
        <div>index_default_slot_top</div>
    
        <?php use_slot('title') ?>
            <?php parent_slot() ?>
            <div>index_title_slot</div>
        <?php use_slot_end() ?>
    
        <?php use_slot('body') ?>
            <div>index_body_slot</div>
        <?php use_slot_end() ?>

        <div>index_default_slot_bot</div>
    <?php component_end() ?>
</div>
<div>index_footer</div>
