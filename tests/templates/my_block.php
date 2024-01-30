<?php

use function Stefmachine\NoTmpl\Render\block;
use function Stefmachine\NoTmpl\Render\end_block;

/** @formatter:off */

?>
<?php block('my_block') ?>
  <div>my_block</div>
<?php end_block() ?>