<?php

use Stefmachine\NoTmpl\Render\NoTmpl;

/** @formatter:off */

?>
<?php NoTmpl::component('component_nested_slots_component.php') ?>
  <?php NoTmpl::slot('my_nested_slot') ?>
    <div>overwritten</div>
  <?php NoTmpl::endSlot() ?>
<?php NoTmpl::endComponent() ?>