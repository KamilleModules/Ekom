<?php


use Core\Services\A;
use Module\Ekom\Api\Layer\CartLayer;

require_once __DIR__ . "/../boot.php";
require_once __DIR__ . "/../init.php";


A::testInit();


$cartModel = CartLayer::create()->getCartModel();


?>
<table>
    <tr>
        <td>Quantité</td>
        <td>Visuel</td>
        <td>Référence</td>
        <td>Vendeur</td>
        <td>Libellé (et attributs)</td>
        <td>Description</td>
        <td>Prix original</td>
        <td>Remise</td>
        <td>Libellé remise</td>
        <td>Prix de base</td>
        <td>Taxe</td>
        <td>Libellé taxe</td>
        <td>Prix de vente</td>
        <td>Prix ligne HT</td>
        <td>Prix ligne TTC</td>
    </tr>
    <?php foreach ($cartModel['items'] as $item): ?>
        <tr>
            <td><?php echo $item['quantityCart']; ?></td>
            <td><img src="<?php echo htmlspecialchars($item['imageThumb']); ?>"
                     alt="<?php echo htmlspecialchars($item['label']); ?>"></td>
            <td><?php echo $item['ref']; ?></td>
            <td><?php echo $item['seller']; ?></td>
            <td><?php echo $item['label']; ?></td>
            <td><?php echo $item['description']; ?></td>
            <td><?php echo $item['priceOriginalRaw']; ?></td>
            <td><?php echo $item['discountRawSavingFixed']; ?></td>
            <td><?php echo $item['discountLabel']; ?></td>
            <td><?php echo $item['priceBaseRaw']; ?></td>
            <td><?php echo $item['taxAmount']; ?></td>
            <td><?php echo $item['taxGroupLabel']; ?></td>
            <td><?php echo $item['priceSaleRaw']; ?></td>
            <td><?php echo $item['priceLineWithoutTaxRaw']; ?></td>
            <td><?php echo $item['priceLineRaw']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<pre>
    <?php
    a($cartModel);
    ?>
</pre>