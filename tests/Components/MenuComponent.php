<?php
/** @formatter:off */
return Component::define('menu', function(array $items) { ?>
    <div>
        <li></li>
        <ul>
            <?php $this->defineSlot('item', function(string $text) { ?>
                <li><?= $text ?></li>
            <?php }) ?>
        </ul>
    </div>
<?php });
