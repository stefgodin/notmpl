<?php

use function Stefmachine\NoTmpl\Render\block;
use function Stefmachine\NoTmpl\Render\end_block;

/** @formatter:off */

?>

<?php block('my_block') ?>
  <div>Before</div>
  <?php block('my_nested_block') ?>
    <div>test</div>
  <?php end_block() ?>
  <div>After</div>
<?php end_block() ?>


<?php block('my_nested_block') ?>
  <div>Overwritten</div>
<?php end_block() ?>
