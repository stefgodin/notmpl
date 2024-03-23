<?php

use function StefGodin\NoTmpl\use_repeat_slots;

/** @formatter:off */
?>

<div>Before</div>
<?php foreach(use_repeat_slots() as $binds): ?>
    <div>has</div>
<?php endforeach ?>
<div>After</div>