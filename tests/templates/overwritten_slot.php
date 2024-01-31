<?php

use function Stefmachine\NoTmpl\Render\end_slot;
use function Stefmachine\NoTmpl\Render\slot;

/** @formatter:off */

?>
<?php slot('my_slot') ?>
  <div>no</div>
<?php end_slot() ?>

<?php slot('my_slot') ?>
  <div>test</div>
<?php end_slot() ?>