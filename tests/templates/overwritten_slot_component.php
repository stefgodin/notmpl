<?php

use Stefmachine\NoTmpl\Render\NoTmpl;

/** @formatter:off */

?>
<?php NoTmpl::component('my_slot.php') ?>
  <?php NoTmpl::slot('my_slot') ?>
    <div>test</div>
  <?php NoTmpl::endSlot() ?>
<?php NoTmpl::endComponent() ?>