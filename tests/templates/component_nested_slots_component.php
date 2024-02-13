<?php

use function StefGodin\NoTmpl\Render\slot;
use function StefGodin\NoTmpl\Render\slot_end;

/** @formatter:off */

?>
<?php slot('my_slot') ?>
  <div>before</div>
  <?php slot('my_nested_slot') ?>
    <div>nestedslot</div>
  <?php slot_end() ?>
  <div>after</div>
<?php slot_end() ?>
