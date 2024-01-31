<?php

use function Stefmachine\NoTmpl\Render\component;
use function Stefmachine\NoTmpl\Render\end_component;
use function Stefmachine\NoTmpl\Render\end_slot;

/** @formatter:off **/
?>
<?php component('non_ended_slot.php') ?>
    <?php end_slot() ?>
<?php end_component() ?>
