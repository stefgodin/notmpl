<?php

namespace StefGodin\NoTmpl;

/** @formatter:off */
?>
<div>component</div>
<?php slot('my_slot') ?>
    <div>default</div>
<?php slot_end() ?>
