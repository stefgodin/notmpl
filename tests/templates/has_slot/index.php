<?php

namespace StefGodin\NoTmpl;

/** @formatter:off */
?>

<?php component('component') ?>
  <?php if(has_slot('my_slot')): ?>
    <?php use_slot('my_slot') ?>
      <div>Tada</div>
    <?php use_slot_end() ?>
  <?php endif ?>
<?php component_end() ?>
