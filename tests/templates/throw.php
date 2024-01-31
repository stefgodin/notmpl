<?php

use function Stefmachine\NoTmpl\Render\slot;

/** @formatter:off */
?>
<?php slot('my_slot') ?>
<?php throw new RuntimeException('An exception') ?>
