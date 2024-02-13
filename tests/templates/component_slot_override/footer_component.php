<?php

use function StefGodin\NoTmpl\slot;
use function StefGodin\NoTmpl\slot_end;

/** @formatter:off */
?>
<div>
    <?php slot() ?>
        <div>footer_default_slot</div>
    <?php slot_end() ?>
</div>