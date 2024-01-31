<?php

use Stefmachine\NoTmpl\Render\NoTmpl;

/** @formatter:off */
?>
<?php NoTmpl::slot('my_slot') ?>
<?php throw new RuntimeException('An exception') ?>
