<?php

namespace StefGodin\NoTmpl;

/** @formatter:off */
?>

<?php slot() ?>
    <div>default</div>
<?php slot_end() ?>

<?php slot('other') ?>
    <div>other</div>
<?php slot_end() ?>

<?php slot('other2') ?>
    <div>other2</div>
<?php slot_end() ?>
