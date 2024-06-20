<?php

namespace StefGodin\NoTmpl;

// @formatter:off
?>
<div><?php esc('<iframe src="https://www.google.com"/>') ?></div>
<div data-thing="<?php esc('"><script>alert(\'hello\')</script>') ?>">Content</div>
