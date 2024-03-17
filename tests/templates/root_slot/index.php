<?php

use function StefGodin\NoTmpl\{slot, slot_end};

/** @formatter:off */
?>

<div>Before</div>
<?php slot() ?>
    <div>Slot content</div>
<?php slot_end() ?>
<div>After</div>