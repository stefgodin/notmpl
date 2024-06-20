<?php


namespace StefGodin\NoTmpl;

/** @formatter:off */
?>

<?php if(has_slot()): ?>
    <div>has</div>
<?php else: ?>
    <div>has not</div>
<?php endif ?>