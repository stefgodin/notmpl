<?php

use function StefGodin\NoTmpl\{component, component_end, use_repeat_slots};

/** @formatter:off */
?>

<?php component('component') ?>
    <?php foreach(use_repeat_slots('no_slot') as $binds): ?>
        <div><?= gettype($binds) ?></div>
    <?php endforeach ?>
<?php component_end() ?>
