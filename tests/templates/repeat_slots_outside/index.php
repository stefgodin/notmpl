<?php

namespace StefGodin\NoTmpl;

/** @formatter:off */
?>

<div>Before</div>
<?php foreach(use_repeat_slots() as $binds): ?>
    <div>has</div>
<?php endforeach ?>
<div>After</div>