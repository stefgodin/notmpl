<?php

use function Stefmachine\NoTmpl\Render\component;
use function Stefmachine\NoTmpl\Render\end_component;
use function Stefmachine\NoTmpl\Render\end_slot;
use function Stefmachine\NoTmpl\Render\slot;

/** @formatter:off */

?>
<?php component('component_nested_slots_component.php') ?>
  <?php slot('my_nested_slot') ?>
    <div>overwritten</div>
  <?php end_slot() ?>
<?php end_component() ?>