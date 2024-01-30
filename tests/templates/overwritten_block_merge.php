<?php

use function Stefmachine\NoTmpl\Render\block;
use function Stefmachine\NoTmpl\Render\end_block;
use function Stefmachine\NoTmpl\Render\merge;

/** @formatter:off */

?>
<?php merge('my_block.php'); ?>

<?php block('my_block') ?>
  <div>test</div>
<?php end_block() ?>