<?php

use function StefGodin\NoTmpl\{component, component_end};

/**
 * @var callable $tag
 */

/** @formatter:off */
?>
<?php component('component.php') ?>
    <?php $tag() ?>
<?php component_end() ?>
