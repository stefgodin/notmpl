<?php

use function Stefmachine\NoTmpl\Render\end_slot;
use function Stefmachine\NoTmpl\Render\slot;

/** @formatter:off */

?>
<?php slot('my_slot') ?>
  <div>my_slot</div>
<?php end_slot() ?>