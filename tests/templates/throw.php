<?php

use function StefGodin\NoTmpl\Render\slot;

/** @formatter:off */
?>
<?php slot('my_slot') ?>
<?php throw new RuntimeException('An exception') ?>
