<?php

use function StefGodin\NoTmpl\slot;
use function StefGodin\NoTmpl\slot_end;

/** @formatter:off */
?>
<?php slot(bindings: ['head' => 'component_head', 'foot' => 'component_foot']) ?><?php slot_end() ?>
