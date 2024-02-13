<?php

use function StefGodin\NoTmpl\Render\slot;
use function StefGodin\NoTmpl\Render\slot_end;

/** @formatter:off */
?>
<?php slot('title') ?><h1>menu-title</h1><?php slot_end() ?>
<ul>
    <?php slot('items')->end() ?>
</ul>
