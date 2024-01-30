<?php

use function Stefmachine\NoTmpl\Render\embed;
use function Stefmachine\NoTmpl\Render\end_slot;
use function Stefmachine\NoTmpl\Render\slot;

/** @formatter:off */

?>
<?php embed('my_slot.php'); ?>

<?php slot('my_slot') ?>
  <div>test</div>
<?php end_slot() ?>