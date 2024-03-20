<?php

use function StefGodin\NoTmpl\{slot, slot_end};

/** @formatter:off */
?>
<div>Component</div>

<?php slot('one_slot', ['id' => 1]) ?>
<?php slot_end() ?>