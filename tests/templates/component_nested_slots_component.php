<?php

use function Stefmachine\NoTmpl\Render\end_slot;
use function Stefmachine\NoTmpl\Render\slot;

/** @formatter:off */

?>
<?php slot('my_slot') ?>
  <div>before</div>
  <?php slot('my_nested_slot') ?>
    <div>nestedslot</div>
  <?php end_slot() ?>
  <div>after</div>
<?php end_slot() ?>
