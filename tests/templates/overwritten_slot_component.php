<?php

use function StefGodin\NoTmpl\Render\component;
use function StefGodin\NoTmpl\Render\component_end;
use function StefGodin\NoTmpl\Render\slot;
use function StefGodin\NoTmpl\Render\slot_end;

/** @formatter:off */

?>
<?php component('my_slot.php') ?>
  <?php slot('my_slot') ?>
    <div>test</div>
  <?php slot_end() ?>
<?php component_end() ?>