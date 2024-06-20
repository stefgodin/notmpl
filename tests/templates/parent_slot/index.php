<?php

namespace StefGodin\NoTmpl;

// @formatter:off
?>

<?php component('test') ?>
    <?php use_slot('first') ?>
        <div>Before first slot</div>
        <?php parent_slot() ?>
        <div>After first slot</div>
    <?php use_slot_end() ?>

    <div>Before default slot</div>
    <?php parent_slot() ?>
    <div>After default slot</div>
<?php component_end() ?>
