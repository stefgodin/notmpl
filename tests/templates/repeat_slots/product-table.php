<?php

use function StefGodin\NoTmpl\{slot, slot_end};

$products = [
    ['id' => 1, 'name' => 'Oreo', 'price' => 25],
    ['id' => 2, 'name' => 'Tomate', 'price' => 12],
    ['id' => 3, 'name' => 'Popcorn', 'price' => 19],
    ['id' => 4, 'name' => 'Patate', 'price' => 66],
];
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
      <?php foreach($products as ['id' => $id, 'name' => $name, 'price' => $price]): ?>
        <tr>
          <td><?= $id ?></td>
          <td>
              <?php slot('product:name', ['id' => $id, 'name' => $name]) ?>
                  <span><?= $name ?></span>
              <?php slot_end() ?>
          </td>
          <td><?= $price ?></td>
        </tr>
      <?php endforeach ?>
    <tr></tr>
  </tbody>
</table>
