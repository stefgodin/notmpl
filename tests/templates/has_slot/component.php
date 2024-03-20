<?php

use function StefGodin\NoTmpl\{slot, slot_end};

/** @formatter:off */
?>
<div>component</div>
<?php slot('my_slot') ?>
    <div>default</div>
<?php slot_end() ?>
