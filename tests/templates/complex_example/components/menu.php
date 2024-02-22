<?php

/**
 * @var array $links
 */

use function StefGodin\NoTmpl\{component, component_end, slot, slot_end};

/** @formatter:off */
?>
<nav>
    <?php slot() ?>
        <?php component('logo') ?>
            <h2>Logo subtitle</h2>
        <?php component_end() ?>
    <?php slot_end() ?>
    <ul>
        <?php foreach($links as $i => $link): ?>
            <li>
                <a href="<?= $link['link'] ?>">
                    <?php slot("link_{$i}", ['link' => $link['link'], 'text' => $link['text']]) ?>
                        <?= $link['text'] ?>
                    <?php slot_end() ?>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
    <?php slot('append-menu')->end() ?>
</nav>
