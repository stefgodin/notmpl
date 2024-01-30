<?php

use function Stefmachine\NoTmpl\Render\block;
use function Stefmachine\NoTmpl\Render\end_block;
use function Stefmachine\NoTmpl\Render\merge;

/** @formatter:off */

?>
<?php block('my_nested_block') ?>
  <div>Overwritten</div>
<?php end_block() ?>
<?php merge("merge_nested_block_merge.php") ?>

