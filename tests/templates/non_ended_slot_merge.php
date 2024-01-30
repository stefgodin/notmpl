<?php

use function Stefmachine\NoTmpl\Render\end_slot;
use function Stefmachine\NoTmpl\Render\merge;

merge('non_ended_slot.php');
end_slot();
