<?php

use function Stefmachine\NoTmpl\Render\end_block;
use function Stefmachine\NoTmpl\Render\merge;

merge('non_ended_block.php');
end_block();
