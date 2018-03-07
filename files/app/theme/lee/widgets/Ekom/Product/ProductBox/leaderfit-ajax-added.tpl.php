<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Theme\LeeTheme;

KamilleThemeHelper::css("product-added-popup.css");


$c = $v['cartModel'];
$m = $v['boxModel'];
$p = $v['itemAdded'];
$isEstimate = (array_key_exists("isEstimate", $v) && true === $v['isEstimate']);
$keyword = 'panier';
$uriBuyProduct = $v['uriCart'];
if (true === $isEstimate) {
    $keyword = 'devis';
    $uriBuyProduct = $v['uriEstimate'];
}


$n = 2;


?>
<div class="container product-added-popup">
    <div class="top-line">
        <h2>AJOUTÉ AU <?php echo strtoupper($keyword); ?> AVEC SUCCÈS</h2>
    </div>
    <div class="main-content">
        <div class="image-container">
            <img src="<?php echo $m['imageMedium']; ?>">
        </div>
        <div class="product-container">
            <div class="title"><?php echo $m['label']; ?></div>
            <div class="ref">Réf. <?php echo $m['ref']; ?></div>


            <!-- FORMATION -->
            <?php if (array_key_exists('trainingInfo', $p)):
                $info = $p['trainingInfo'];
                ?>
                <div class="product-details" style="margin-top: 10px">
                    <div>
                        <span class="label">Ville: </span>
                        <span class="value"><?php echo $info['selectedCityLabel']; ?></span>
                    </div>
                    <div>
                        <span class="label">Date: </span>
                        <span class="value"><?php echo $info['selectedDateRangeLabel']; ?></span>
                    </div>

                </div>
            <?php endif; ?>


            <?php
            if ($m['rating_nbVotes'] > 0):

                $n = ceil($m['rating_amount'] / 20); // 20 = 100 / 5stars
                ?>
                <div class="rating-container">
                    <div class="rating">
                        <?php for ($i = 5; $i > 0; $i--): ?>
                            <?php if ((int)$n === $i): ?>
                                <span class="hover">☆</span>
                            <?php else: ?>
                                <span>☆</span>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <div class="text">
                        ({rating_nbVotes} avis)
                    </div>
                </div>
            <?php endif; ?>


            <div class="weight">
                <span class="label">Poids:</span>
                <span class="value"><?php echo round($m['weight'], 2); ?>kg</span>
            </div>
            <div class="quantity">
                <span class="label">Quantité:</span>
                <span class="value"><?php echo $p['quantity']; ?></span>
            </div>
            <div class="price">
                <span class="label">Prix unitaire:</span>
                <span class="value price t-price"><?php echo $m['salePrice']; ?> <abbr>TTC</abbr></span>
            </div>
            <a href="<?php echo $p['uri_card_with_details']; ?>" class="lee-gray-button product-link">MODIFIER MON
                PRODUIT</a>
        </div>
        <div class="cart-info">
            <div class="line">Votre <?php echo $keyword; ?> contient <?php echo $c['totalQuantity']; ?> articles</div>
            <table class="cart-summary">
                <tr>
                    <td>SOUS-TOTAL HT</td>
                    <td class="bold t-price"><?php echo $c['linesTotalWithoutTax']; ?></td>
                </tr>
                <tr class="small-height">
                    <td colspan="2" class="small-text">Frais de port calculés à l'étape suivante</td>
                </tr>
                <tr>
                    <td>TVA 20%</td>
                    <td class="bold t-price"><?php
                        /**
                         * @todo-ling: TVA is not all taxes, fix this one way or the other...
                         */
                        echo $c['taxAmount']; ?></td>
                </tr>
                <tr>
                    <td>TOTAL TTC</td>
                    <td class="bold t-price"><?php echo $c['linesTotalWithTax']; ?></td>
                </tr>
                <tr class="small-height">
                    <td colspan="2" class="small-text">Vous cumulez <b>60 points</b> sur cette commande</td>
                </tr>
                <tr style="height:70px;">
                    <td class="order-total">TOTAL DE LA COMMANDE</td>
                    <td class="bold t-price"><?php echo $c['cartTotalWithTax']; ?></td>
                </tr>
            </table>
            <a href="<?php echo $uriBuyProduct; ?>" class="lee-red-button">
                FINALISER MON <?php echo strtoupper($keyword); ?>
            </a>
        </div>
    </div>

    <div class="resume-purchase-link-container">
        <a href="#" class="resume-purchase-link">Continuer mes achats</a>
    </div>
</div>

<script>
    $(document).ready(function () {
        var jLink = $('.resume-purchase-link');
        jLink.on('click', function () {
            var current = $.featherlight.current();
            current.close();
            return false;
        });
    });
</script>

