<?php

use function Stefmachine\NoTmpl\Render\component;
use function Stefmachine\NoTmpl\Render\end_component;
use function Stefmachine\NoTmpl\Render\end_slot;
use function Stefmachine\NoTmpl\Render\slot;

/** @formatter:off */

?>
<?php component('my_slot.php') ?>
  <?php slot('my_slot') ?>
    <div>test</div>
  <?php end_slot() ?>
<?php end_component() ?>