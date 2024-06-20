<?php

namespace StefGodin\NoTmpl;

/**
 * @var array $products
 */

/** @formatter:off */
?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($products as $product): ?>
            <tr>
                <td><?php slot('id', ['product' => $product]) ?><?= $product['id'] ?><?php slot_end() ?></td>
                <td><?php slot('name', ['product' => $product]) ?><?= $product['name'] ?><?php slot_end() ?></td>
                <td><?php slot('price', ['product' => $product]) ?><?= $product['price'] ?><?php slot_end() ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>