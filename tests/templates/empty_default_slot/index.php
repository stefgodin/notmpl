<?php

namespace StefGodin\NoTmpl;

/** @formatter:off */
?>

<?php component('component') ?>

    <?php use_slot('other') ?>
        <div>index other</div>
    <?php use_slot_end() ?>

    <?php use_slot('other2') ?>
        <div>index other2</div>
    <?php use_slot_end() ?>

<?php component_end() ?>
