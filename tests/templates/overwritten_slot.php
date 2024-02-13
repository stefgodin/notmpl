<?php

use function StefGodin\NoTmpl\Render\slot;
use function StefGodin\NoTmpl\Render\slot_end;

/** @formatter:off */

?>
<?php slot('my_slot') ?>
  <div>no</div>
<?php slot_end() ?>

<?php slot('my_slot') ?>
  <div>test</div>
<?php slot_end() ?>