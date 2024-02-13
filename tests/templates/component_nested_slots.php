<?php

use function StefGodin\NoTmpl\Render\component;
use function StefGodin\NoTmpl\Render\component_end;
use function StefGodin\NoTmpl\Render\slot;
use function StefGodin\NoTmpl\Render\slot_end;

/** @formatter:off */

?>
<?php component('component_nested_slots_component.php') ?>
  <?php slot('my_nested_slot') ?>
    <div>overwritten</div>
  <?php slot_end() ?>
<?php component_end() ?>