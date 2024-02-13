<?php

use function StefGodin\NoTmpl\component;
use function StefGodin\NoTmpl\component_end;
use function StefGodin\NoTmpl\parent_slot;
use function StefGodin\NoTmpl\use_slot;
use function StefGodin\NoTmpl\use_slot_end;

/**
 * @var string $title
 */

/** @formatter:off */
?>

<div>index_header</div>
<div>
    <?php component('page', ['title' => $title]) ?>
        <div>index_default_slot_top</div>
    
        <?php use_slot('header', $headerBinds) ?>
            <?php parent_slot() ?>
            <div>index_header_slot</div>
            <div><?= $headerBinds['header'] ?></div>
        <?php use_slot_end() ?>
    
        <?php use_slot('title') ?>
            <?php parent_slot() ?>
            <div>index_title_slot</div>
        <?php use_slot_end() ?>
    
        <?php use_slot('body') ?>
            <div>index_body_slot</div>
        <?php use_slot_end() ?>
    
        <?php use_slot('footer') ?>
            <?php parent_slot() ?>
            <div>index_footer_slot</div>
        <?php use_slot_end() ?>

        <div>index_default_slot_bot</div>
    <?php component_end() ?>
</div>
<div>index_footer</div>
