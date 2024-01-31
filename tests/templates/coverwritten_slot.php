<?php

use Stefmachine\NoTmpl\Render\NoTmpl;

/** @formatter:off */

?>
<?php NoTmpl::slot('my_slot') ?>
  <div>no</div>
<?php NoTmpl::endSlot() ?>

<?php NoTmpl::slot('my_slot') ?>
  <div>test</div>
<?php NoTmpl::endSlot() ?>