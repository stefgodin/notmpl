<?php

use function StefGodin\NoTmpl\{slot, slot_end};

/** @formatter:off */
?>
<div>
    <img src="/logo.png" alt="logo title">
    <?php slot(bindings: ['default' => 'default_title']) ?>
        <h2>Default logo title</h2>
    <?php slot_end() ?>
</div>