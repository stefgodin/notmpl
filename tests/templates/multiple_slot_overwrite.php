<?php

use Stefmachine\NoTmpl\Render\NoTmpl;

/** @formatter:off */
?>
<?php NoTmpl::slot('my_slot') ?>
    <div>test</div>
<?php NoTmpl::endSlot(); ?>
<?php NoTmpl::slot('my_slot') ?>
    <div>test2</div>
<?php NoTmpl::endSlot(); ?>
<?php NoTmpl::slot('my_slot') ?>
    <div>test3</div>
<?php NoTmpl::endSlot(); ?>