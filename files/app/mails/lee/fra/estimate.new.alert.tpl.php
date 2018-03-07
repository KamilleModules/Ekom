<?php require_once __DIR__ . "/inc/top.php"; ?>


<tr style="height: 20px;">
    <td class="content" style="padding: 30px;">

        <strong style="font-size: 14px;font-weight: 700;">Bonjour {teammate_name},</strong>
        <br>
        <br>
        un devis vient d'être réalisé sur le site {site_name}.<br>
        Heure du serveur: {date}.<br>




        <table cellpadding="0" cellspacing="0" class="table"
               style="width: 100%;margin-top: 5px;border-spacing: 0;border: 1px solid #525252;text-align: left;">
            <thead>
            <tr style="height: 20px;">
                <th style="background-color: #525252;color: #ffffff;padding: 5px;">Informations sur le client</th>
            </tr>
            </thead>
            <tbody>
            <tr style="height: 20px;">
                <td style="padding: 5px;border: 1px solid #525252;"><a href="{uri_customer_info}">Voir les informations sur ce client</a></td>
            </tr>
            </tbody>
        </table>



        <br>
        DÉTAILS DU DEVIS<br>
        N°: {order_id}<br>
        Date: {order_date}<br>
        <br>


        <table cellpadding="0" cellspacing="0" style="width: 100%;margin-top: 5px;border-spacing: 0;">
            <tr style="height: 20px;">
                <td width="580">
                    <table cellpadding="0" cellspacing="0" class="table"
                           style="border-right: 0;width: 100%;margin-top: 5px;border-spacing: 0;border: 1px solid #525252;text-align: left;border-right:0;">
                        <thead>
                        <tr class="center" style="height: 20px;text-align: center;">
                            <th style="background-color: #525252;color: #ffffff;padding: 5px;text-align: left">Référence</th>
                            <th style="background-color: #525252;color: #ffffff;padding: 5px;text-align: left">Produit</th>
                            <th style="background-color: #525252;color: #ffffff;padding: 5px;text-align: left">Quantité</th>
                            <th style="background-color: #525252;color: #ffffff;padding: 5px;text-align: left">Prix unit. HT</th>
                            <th style="background-color: #525252;color: #ffffff;padding: 5px;text-align: left">Total HT</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($variables) && is_array($variables) && array_key_exists("items", $variables)):
                            $items = $variables['items'];
                            ?>
                            <?php foreach ($items as $item): ?>
                            <tr class="tr-large center" style="height: 50px;text-align: center;">
                                <td style="padding: 5px;border: 1px solid #525252;text-align: left"><?php echo $item['ref']; ?></td>
                                <td style="padding: 5px;border: 1px solid #525252;text-align: left">
                                    <?php echo $item['label']; ?>
                                    <?php if ($item['attributesSelection']): ?>
                                        <br>
                                        <?php foreach ($item['attributesSelection'] as $attr): ?>
                                            <?php echo $attr["name_label"]; ?>: <?php echo $attr['value_label']; ?>
                                            <br>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <?php if ($item['productDetailsSelection']): ?>
                                        <br>
                                        <?php foreach ($item['productDetailsSelection'] as $attr): ?>
                                            <?php echo $attr["name_label"]; ?>: <?php echo $attr['value_label']; ?>
                                            <br>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 5px;border: 1px solid #525252;text-align: left"><?php echo $item['quantityCart']; ?></td>
                                <td style="padding: 5px;border: 1px solid #525252;text-align: left"><?php echo $item['priceBaseRaw']; ?></td>
                                <td width="200"
                                    style="padding: 5px;border: 1px solid #525252;text-align: left"><?php echo $item['priceLineWithoutTaxRaw']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="tr-large center" style="height: 50px;text-align: center;">
                                <td style="padding: 5px;border: 1px solid #525252;text-align: left">$id_product</td>
                                <td style="padding: 5px;border: 1px solid #525252;text-align: left">$product_name</td>
                                <td style="padding: 5px;border: 1px solid #525252;text-align: left">$product_amount</td>
                                <td style="padding: 5px;border: 1px solid #525252;text-align: left">$product_price</td>
                                <td width="200" style="padding: 5px;border: 1px solid #525252;text-align: left">$total</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>


        <br>
        <table cellpadding="0" cellspacing="0" class="table"
               style="width: 100%;margin-top: 5px;border-spacing: 0;border: 1px solid #525252;text-align: left;">
            <tr style="height: 20px;">
                <td style="padding: 5px;border: 1px solid #525252;">Total produits HT</td>
                <td width="200" class="center" style="text-align: right;padding: 5px;border: 1px solid #525252;">
                    {cart_total_without_tax}
                </td>
            </tr>
            <tr style="height: 20px;">
                <td style="padding: 5px;border: 1px solid #525252;">Taxes</td>
                <td width="200" class="center" style="text-align: right;padding: 5px;border: 1px solid #525252;">
                    {tax_amount}
                </td>
            </tr>
            <tr style="height: 20px;">
                <td style="padding: 5px;border: 1px solid #525252;">Frais de port</td>
                <td width="200" class="center" style="text-align: right;padding: 5px;border: 1px solid #525252;">
                    {shipping_cost_with_tax}
                </td>
            </tr>
            <tr style="height: 20px;">
                <td style="padding: 5px;border: 1px solid #525252;">Coupons</td>
                <td width="200" class="center" style="text-align: right;padding: 5px;border: 1px solid #525252;">
                    {coupon_saving}
                </td>
            </tr>
            <tr style="height: 20px;">
                <td style="padding: 5px;border: 1px solid #525252;">Net à payer</td>
                <td width="200" class="center" style="text-align: right;padding: 5px;border: 1px solid #525252;">
                    {order_grand_total}
                </td>
            </tr>
        </table>
        <br>
        <table cellpadding="0" cellspacing="0" class="table"
               style="width: 100%;margin-top: 5px;border-spacing: 0;border: 1px solid #525252;text-align: left;">
            <thead>
            <tr style="height: 20px;">
                <th style="background-color: #525252;color: #ffffff;padding: 5px;">Informations supplémentaires</th>
            </tr>
            </thead>
            <tbody>
            <tr style="height: 20px;">
                <td style="padding: 5px;border: 1px solid #525252;">Transporteur: {carrier_details_string}</td>
            </tr>
            <tr style="height: 20px;">
                <td style="padding: 5px;border: 1px solid #525252;">Commentaire livraison: {shipping_comment}</td>
            </tr>
            </tbody>
        </table>
        <br>
        <table cellpadding="0" cellspacing="0" style="width: 100%;margin-top: 5px;border-spacing: 0;">
            <tr style="height: 20px;">
                <td width="580" class="responsive">
                    <table cellpadding="0" cellspacing="0" class="table"
                           style="width: 100%;margin-top: 5px;border-spacing: 0;border: 1px solid #525252;text-align: left;">
                        <thead>
                        <tr style="height: 20px;">
                            <th style="background-color: #525252;color: #ffffff;padding: 5px;">ADRESSE DE LIVRAISON</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="tr-large" style="height: 50px;">
                            <td valign="top" style="padding: 5px;border: 1px solid #525252;">{delivery_address}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td class="hide">
                    &nbsp;&nbsp;
                </td>
                <td width="580" class="responsive">
                    <table cellpadding="0" cellspacing="0" class="table"
                           style="width: 100%;margin-top: 5px;border-spacing: 0;border: 1px solid #525252;text-align: left;">
                        <thead>
                        <tr style="height: 20px;">
                            <th style="background-color: #525252;color: #ffffff;padding: 5px;">ADRESSE DE FACTURATION
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="tr-large" style="height: 50px;">
                            <td valign="top" style="padding: 5px;border: 1px solid #525252;">{billing_address}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>

            </tr>
        </table>

    </td>
</tr>


<?php require_once __DIR__ . "/inc/bottom.php"; ?>
