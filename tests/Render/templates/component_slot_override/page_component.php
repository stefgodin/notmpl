<?php

use function StefGodin\NoTmpl\slot;
use function StefGodin\NoTmpl\slot_end;

/** @formatter:off */
?>

<!--page_component-->
<div>page_header</div>

<div>
    <?php slot('title') ?>
        <div>page_title_slot</div>
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
    <?php slot('footer') ?>
        <div>page_footer_slot</div>
    <?php slot_end() ?>
</div>
