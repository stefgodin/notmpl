<?php

use function StefGodin\NoTmpl\Render\component;
use function StefGodin\NoTmpl\Render\component_end;
use function StefGodin\NoTmpl\Render\slot_end;

/** @formatter:off **/
?>
<?php component('non_ended_slot.php') ?>
    <?php slot_end() ?>
<?php component_end() ?>
