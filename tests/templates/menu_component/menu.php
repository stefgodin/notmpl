<?php

use function Stefmachine\NoTmpl\Render\end_slot;
use function Stefmachine\NoTmpl\Render\slot;

/** @formatter:off */
?>
<?php slot('title') ?><h1>menu-title</h1><?php end_slot() ?>
<ul>
    <?php slot('items')->end() ?>
</ul>
