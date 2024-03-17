<?php

use function StefGodin\NoTmpl\{slot, slot_end};

// @formatter:off
?>
<div>
    <?php slot('first') ?>
        <div>first</div>
    <?php slot_end() ?>
</div>
<div>
    <?php slot() ?>
        <div>default</div>
    <?php slot_end() ?>
</div>
