<?php

use Stefmachine\NoTmpl\Render\NoTmpl;

/** @formatter:off */

?>

<?php NoTmpl::slot('my_slot') ?>
  <div>before</div>
  <?php NoTmpl::slot('my_nested_slot') ?>
    <div>test</div>
  <?php NoTmpl::endSlot() ?>
  <div>after</div>
<?php NoTmpl::endSlot() ?>


<?php NoTmpl::slot('my_nested_slot') ?>
  <div>overwritten</div>
<?php NoTmpl::endSlot() ?>
