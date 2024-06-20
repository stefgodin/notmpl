<?php


namespace StefGodin\NoTmpl;

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
<div>Before</div>
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

            <span attr="<?php esc($title) ?>"><?= $title ?></span>
            <?php parent_slot() ?>
            <span><?php esc($title.' '.gettype($binds)) ?></span>
        <?php component_end() ?>
    </div>
<?php slot_end() ?>

<div>
    <?php slot() ?>
        <?php component('components/products') ?>
            <?php use_slot('name', $bindings) ?>
                FIRST!
            <?php use_slot_end() ?>
    
            <?php foreach(use_repeat_slots('name') as $k => $bindings): ?>
                <span><?php parent_slot() ?>(<?= $bindings['product']['id'] ?>) : <?= $k ?></span>
                <?php if($k === 2){ ?>
                    <?php use_slot_end(); break; ?>
                <?php } ?>
            <?php endforeach ?>
        <?php component_end() ?>
    <?php slot_end() ?>
</div>

<?php component('footer')->end() ?>