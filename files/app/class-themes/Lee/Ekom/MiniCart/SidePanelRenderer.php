<?php


namespace Theme\Lee\Ekom\MiniCart;


use Module\Ekom\Utils\E;

class SidePanelRenderer
{


    public static function renderMiniCartInner(array $cartModel)
    {
        ob_start();
        $sClass = "lee-hidden";
        if ($cartModel['cartTotalQuantity'] > 0) {
            $sClass = "";
        }
        ?>
        <div class="numberCircle panel-trigger <?php echo $sClass; ?>"><span
                    class="panel-trigger total-quantity-mini"><?php echo $cartModel['cartTotalQuantity']; ?></span>
        </div>
        <span class="panel-trigger lee-icon action action-cart">Panier</span>
        <?php
        return ob_get_clean();
    }

    public static function renderTop(array $cartModel)
    {
        ob_start();
        ?>
        <header class="cd-panel-header">
            <h1>Mon Panier (<span class="total-quantity"><?php echo $cartModel['cartTotalQuantity']; ?></span> produits)
            </h1>
            <a href="#0" class="cd-panel-close">Close</a>
        </header>
        <?php
        return ob_get_clean();
    }

    public static function render(array $cartModel)
    {
        $cartItemRenderer = CartItemRenderer::create();
        $v = $cartModel;

        ob_start();

        ?>
        <div class="mini-cart-content dropdown-content left-hand skip-content skip-content--style block-cart block">


            <ol id="cart-sidebar" class="mini-products-list clearer">

                <?php
                $c = 0;
                $max = count($v['items']);
                foreach ($v['items'] as $item):

                    $sFirst = (0 === $c) ? 'first' : '';
                    $c++;


                    ?>
                    <li data-token="<?php echo $item['cartToken']; ?>"
                        class="cart-item <?php echo $sFirst; ?> bionic-context">

                        <div class="left">
                            <a href="<?php echo $item['uriProductInstance']; ?>">
                                <img width="120" height="100" src="<?php echo $item['imageThumb']; ?>">
                            </a>
                        </div>
                        <div class="middle">
                            <a class="product-label"
                               href="<?php echo $item['uriProductInstance']; ?>"><?php echo $item['label']; ?></a>
                            <span class="ref">Réf: <?php echo $item['ref']; ?></span>
                            <div class="attributes">
                                <?php if ($item['attributesSelection']): ?>
                                    <?php foreach ($item['attributesSelection'] as $attr): ?>
                                        <div class="attribute-item">
                                            <span class="label"><?php echo $attr['name_label']; ?>: </span>
                                            <span class="value"><?php echo $attr['value_label']; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php $cartItemRenderer->renderItem($item); ?>
                            </div>


                            <div class="attributes-after">

                                <!-- start-add-on: EkomCardCombination module -->
                                <?php if (array_key_exists('eccCombinationSummary', $item)): ?>
                                    <div class="ekom-card-combination-items">
                                        <?php
                                        foreach ($item['eccCombinationSummary'] as $cardLabel => $attr):
                                            ?>
                                            <div class="ekom-card-combination-item">
                                                <span class="label"><?php echo $cardLabel; ?></span>
                                                <div class="attributes">
                                                    <?php foreach ($attr as $attrName => $attrValue): ?>
                                                        <span class="attribute"><?php echo $attrValue; ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <!-- end-add-on: EkomCardCombination module -->
                            </div>

                            <div class="quantity-input-container">
                                <input class="quantity-input bionic-number" type="number"
                                       data-action="cart.updateItemQuantity"
                                       data-param-token="<?php echo $item['cartToken']; ?>"
                                       data-param-quantity="$this"
                                       data-ninshadow="ninshadow"
                                       min="1"
                                       max="<?php echo (-1 !== (int)$item['quantityStock']) ? $item['quantityStock'] : 10000000; ?>"
                                       value="<?php echo $item['quantityCart']; ?>">
                                <div class="nin-shadow-loader bionic-target" data-id="ninshadow"></div>
                            </div>
                        </div>
                        <div class="right">
                            <a class="remove-item bionic-btn" href="#"
                               data-action="cart.removeItem"
                               data-param-token="<?php echo $item['cartToken']; ?>"
                               data-ninshadow="ninshadow"
                            >Supprimer</a>
                            <div class="price t-price line-price">
                                <?php echo $item['priceLine']; ?>
                                <?php if ($item['taxHasTax']): ?>
                                    <abbr>TTC</abbr>
                                <?php else: ?>
                                    <abbr>HT</abbr>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>

            <div class="bottom-part">
                <table class="price-table">
                    <tr>
                        <td>TOTAL HT</td>
                        <td class="total-without-tax t-price"><?php echo $v['priceCartTotalWithoutTax']; ?></td>
                    </tr>
                    <tr>
                        <td>TOTAL TTC</td>
                        <td class="total-with-tax t-price"><?php echo $v['priceCartTotal']; ?></td>
                    </tr>
                </table>

                <div>
                    (Estimation des frais de port à l'étape suivante)
                </div>


                <div class="clearer bottom-actions">
                    <button
                            onclick="window.location.href='<?php echo E::link("Ekom_cart"); ?>'; return false;"
                            type="button" class="button btn-inline lee-red-button">TERMINER MA COMMANDE
                    </button>
                    <button
                            type="button" class="button btn-inline lee-black-button bionic-btn"
                            data-action="estimateCart.ekomCart2EkomEstimateCart"
                    >
                        TRANSFORMER EN DEVIS
                    </button>

                    <?php
                    //
                    //                        <!--                        <button type="button" title="Proceed to Checkout"-->
                    //<!--                                class="button btn-checkout btn-inline " onclick="window.location.href='--><?php //echo E::link("Ekom_checkoutOnePage");
                    //'; return false;"><span><span>Proceed to Checkout</span></span></button>
                    ?>

                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}