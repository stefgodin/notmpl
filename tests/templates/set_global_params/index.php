<?php

use function StefGodin\NoTmpl\component;

$test1 ??= 11;
$test2 ??= 12;
$test3 ??= 13;
$test4 ??= 14;

/** @formatter:off */
?>
<div>Index</div>
<div><?= $test1 ?></div>
<div><?= $test2 ?></div>
<div><?= $test3 ?></div>
<div><?= $test4 ?></div>
<?php component('component', ['test4' => 6])->end() ?>