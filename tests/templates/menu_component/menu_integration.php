<?php

use function Stefmachine\NoTmpl\Render\component;
use function Stefmachine\NoTmpl\Render\end_component;
use function Stefmachine\NoTmpl\Render\end_slot;
use function Stefmachine\NoTmpl\Render\parent_slot;
use function Stefmachine\NoTmpl\Render\slot;

/**
 * @var string[] $items
 */

/** @formatter:off **/
?>
<div>
    <?php component('menu') ?>
        <?php slot('title') ?>
            <?php parent_slot() ?>
            <?php component('menu-title')->end() ?>
        <?php end_slot() ?>
        <?php slot('items') ?>
            <?php foreach($items as $item): ?>
                <?php component('menu-item', ['text' => $item])->end() ?>
            <?php endforeach ?>
        <?php end_slot() ?>
    <?php end_component() ?>
</div>