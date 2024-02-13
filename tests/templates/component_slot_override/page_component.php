<?php

use function StefGodin\NoTmpl\component;
use function StefGodin\NoTmpl\component_end;
use function StefGodin\NoTmpl\slot;
use function StefGodin\NoTmpl\slot_end;

/**
 * @var string $title
 */

/** @formatter:off */
?>

<!--page_component-->
<div>
    <?php slot('header', ['header' => 'header']) ?>
        <div>page_header</div>
    <?php slot_end() ?>
</div>

<div>
    <?php slot('title') ?>
        <div>page_title_slot</div>
        <h1><?= $title ?></h1>
    <?php slot_end() ?>
</div>

<div>
    <?php slot('body') ?>
        <div>page_body_slot</div>
    <?php slot_end() ?>
</div>

<div>
    <?php slot() ?>
        <div>page_default_slot</div>
    <?php slot_end() ?>
</div>

<div>
    <?php component('footer') ?>
        <?php slot('footer') ?>
            <div>page_footer_default_slot</div>
        <?php slot_end() ?>
    <?php component_end() ?>
</div>
