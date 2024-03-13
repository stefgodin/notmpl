<?php
/** @noinspection PhpUndefinedVariableInspection */

/** @noinspection PhpUndefinedFunctionInspection */

/** @noinspection PhpArrayAccessOnIllegalTypeInspection */

/** @noinspection PhpPassByRefInspection */

/** @formatter:off */
?>

<?php component('test') ?>
    <?php foreach(use_repeated_slot('name') as $i => $binds): ?>
        <div><?= $binds['a'] ?></div>
    <?php endforeach ?>
<?php component_end() ?>