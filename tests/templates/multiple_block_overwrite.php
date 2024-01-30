<?php

use function Stefmachine\NoTmpl\Render\block;
use function Stefmachine\NoTmpl\Render\end_block;

/** @formatter:off */
?>
<?php block('my_block') ?>
    <div>test</div>
<?php end_block(); ?>
<?php block('my_block') ?>
    <div>test2</div>
<?php end_block(); ?>
<?php block('my_block') ?>
    <div>test3</div>
<?php end_block(); ?>