<?php

namespace StefGodin\NoTmpl;

/** @formatter:off */
?>

<?php component('component') ?>
  <?php if(has_slot('my_slot')): ?>
    <?php use_slot('my_slot') ?>
      <?php throw new \RuntimeException('Not supposed to get here') ?>
    <?php use_slot_end() ?>
  <?php endif ?>
<?php component_end() ?>
