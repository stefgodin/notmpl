<?php

use function StefGodin\NoTmpl\slot;
use function StefGodin\NoTmpl\slot_end;

/** @formatter:off */
?>
<?php slot(bindings: ['head' => 'subcomponent_head', 'foot' => 'subcomponent_foot']) ?><?php slot_end() ?>
