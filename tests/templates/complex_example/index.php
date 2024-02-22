<?php

use function StefGodin\NoTmpl\{component, component_end, esc_html, parent_slot, slot, slot_end, use_slot, use_slot_end};

/**
 * @var string $title
 * @var null $binds
 */

$links = [
    ['link' => '/', 'text' => 'Home'],
    ['link' => '/about', 'text' => 'About'],
    ['link' => '/contact', 'text' => 'Contact'],
];

/** @formatter:off */
?>
<?php slot('head', ['links' => $links]) ?>
    <div>
        <?php component('components/menu', ['links' => $links]) ?>
            <?php foreach($links as $i => $link): ?>
                <?php use_slot("link_{$i}", $binds) ?>
                    <?php parent_slot() ?>
                    <strong><?= $binds['text'] ?></strong>
                <?php use_slot_end() ?>
            <?php endforeach ?>
        
            <?php use_slot('undefined', $binds) ?>
                Nothing to overwrite
            <?php use_slot_end() ?>

            <span attr="<?= esc_html($title) ?>"><?= $title ?></span>
              <?php parent_slot() ?>
            <span><?= esc_html($title.' '.gettype($binds)) ?></span>
        <?php component_end() ?>
    </div>
<?php slot_end() ?>

<div>
    <?php slot() ?>
        <?php component('components/products')->end() ?>
    <?php slot_end() ?>
</div>

<?php component('footer')->end() ?>