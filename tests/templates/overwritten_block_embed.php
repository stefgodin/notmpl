<?php

use function Stefmachine\NoTmpl\Render\block;
use function Stefmachine\NoTmpl\Render\embed;
use function Stefmachine\NoTmpl\Render\end_block;

/** @formatter:off */

?>
<?php embed('my_block.php'); ?>

<?php block('my_block') ?>
  <div>test</div>
<?php end_block() ?>