<?php

use function Stefmachine\NoTmpl\Render\end_slot;
use function Stefmachine\NoTmpl\Render\slot;

/** @formatter:off */

?>

<?php slot('my_slot') ?>
  <div>before</div>
  <?php slot('my_nested_slot') ?>
    <div>test</div>
  <?php end_slot() ?>
  <div>after</div>
<?php end_slot() ?>


<?php slot('my_nested_slot') ?>
  <div>overwritten</div>
<?php end_slot() ?>
