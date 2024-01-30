<?php

use function Stefmachine\NoTmpl\Render\end_slot;
use function Stefmachine\NoTmpl\Render\merge;
use function Stefmachine\NoTmpl\Render\slot;

/** @formatter:off */

?>
<?php slot('my_nested_slot') ?>
  <div>Overwritten</div>
<?php end_slot() ?>
<?php merge("merge_nested_slot_merge.php") ?>

