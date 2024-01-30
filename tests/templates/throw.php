<?php

use function Stefmachine\NoTmpl\Render\merge;
use function Stefmachine\NoTmpl\Render\slot;

/** @formatter:off */
?>
<?php merge('basic.php') ?>
<?php slot('my_slot') ?>
<?php throw new RuntimeException('An exception') ?>
