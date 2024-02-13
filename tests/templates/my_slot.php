<?php

use function StefGodin\NoTmpl\Render\slot;
use function StefGodin\NoTmpl\Render\slot_end;

/** @formatter:off */

?>
<?php slot('my_slot') ?>
  <div>my_slot</div>
<?php slot_end() ?>