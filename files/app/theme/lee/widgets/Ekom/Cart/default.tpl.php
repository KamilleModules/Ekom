<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Theme\LeeTheme;

KamilleThemeHelper::css("cart.css");

//EkomApi::inst()->cartLayer()->setCouponBag([]);


?>



<?php if (0 === $v['totalQuantity']): ?>
    <div>
        Veuillez ajouter des produits à votre panier
    </div>
<?php else: ?>

    <div class="cart" id="cart">
        <div class="product-list">
            <?php foreach ($v['items'] as $item):


                $stockClass = "";
                if ('stockAvailable' === $item['stockType']) {
                    $stockClass = "availability-in-stock";
                } elseif ('outOfStock' === $item['stockType']) {
                    $stockClass = "availability-out-of-stock";
                }

                ?>
                <div class="item" data-id="<?php echo $item['product_id']; ?>">
                    <div class="product-bar">
                        <div class="image">
                            <img width="120" src="<?php echo $item['imageSmall']; ?>"
                                 alt="<?php echo htmlspecialchars($item['label']); ?>">
                        </div>
                        <div class="product-info">
                            <div class="label"><a
                                        href="<?php echo $item['uri_card_with_ref']; ?>"><?php echo $item['label']; ?></a>
                            </div>
                            <div class="ref">Réf: <?php echo $item['ref']; ?></div>
                            <div class="attributes">
                                <?php foreach ($item['attributes'] as $at): ?>
                                    <span class="attribute-name"><?php echo $at['label']; ?></span>: <span
                                            class="attribute-value"><?php echo $at['value']; ?></span>
                                <?php endforeach ?>
                            </div>
                            <div class="stock-info <?php echo $stockClass; ?>"><?php echo $item['stockText']; ?></div>
                        </div>
                        <div class="quantity-container">
                            <div class="label">Quantité</div>
                            <div>
                                <input class="quantity-input" type="number" value="<?php echo $item['quantity']; ?>">
                            </div>
                        </div>
                        <div class="price-container">
                            <?php if (true === $v['isB2B']): ?>
                                <div class="price-to-pay"><?php echo $item['linePrice']; ?> <em>HT</em></div>
                            <?php else: ?>
                                <div class="price-with-tax"><?php echo $item['linePrice']; ?> <em>TTC</em></div>
                                <div class="price-without-tax"><?php echo $item['linePrice']; ?> <em>HT</em></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="action-bar">
                        <a href="#" class="product-remove-btn">Supprimer</a>
                        <a href="#">Ajouter aux favoris</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>


        <?php
        $sCouponClass = "";
        if (true === $v['hasCoupons']) {
            $sCouponClass = "visible";

        }
        ?>

        <div class="right-block">
            <div class="cart-summary">
                <h1>Votre commande</h1>
                <table>
                    <tr>
                        <td>Total commande</td>
                        <?php if (true === $v['isB2B']): ?>
                            <td class="price"><span class="total-with-tax">{linesTotal}</span> <em>HT</em></td>
                        <?php else: ?>
                            <td class="price"><span class="total-with-tax">{linesTotal}</span> <em>TTC</em></td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td>Dont TVA</td>
                        <td class="price"><span class="tax-amount">{taxAmount}</span> <em>TTC</em></td>
                    </tr>
                    <tr class="promo <?php echo $sCouponClass; ?>">
                        <td>Code promo</td>
                        <td class="price total-saving">{totalSaving}</td>
                    </tr>
                    <?php
                    $c = 0;
                    foreach ($v['coupons'] as $coupon): ?>
                        <tr class="coupon-details <?php echo $sCouponClass; ?>">
                            <td>( <?php echo $coupon['label']; ?> <?php echo $coupon['saving']; ?> )</td>
                            <td><a href="#" class="coupon-remove-btn" data-index="<?php echo $c++; ?>">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <tr class="promo-message-error">
                        <td colspan="2">
                            <div class="promo-msg-error"></div>
                        </td>
                    </tr>
                    <tr class="promo-message-success">
                        <td colspan="2">
                            <div class="promo-msg-success"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>Frais de port estimés</td>
                        <td class="price"><span class="total-shipping-cost">{estimatedTotalShippingCost}</span>
                            <em>TTC</em></td>
                    </tr>
                    <tr class="sep">
                        <td colspan="2"></td>
                    </tr>
                    <tr class="total">
                        <td>TOTAL</td>
                        <?php if (true === $v['isB2B']): ?>
                            <td class="price"><span class="order-grand-total">{estimatedOrderGrandTotal}</span>
                                <em>HT</em></td>
                        <?php else: ?>
                            <td class="price"><span class="order-grand-total">{estimatedOrderGrandTotal}</span>
                                <em>TTC</em></td>
                        <?php endif; ?>
                    </tr>
                </table>
            </div>
            <div class="coupon-form block">
                Code promo:
                <input type="text" class="coupon-add-input">
                <button class="front-button button-gray coupon-add-btn">OK</button>
            </div>
            <div class="block">
                <button class="checkout-link front-button button-red">VALIDER MON PANIER</button>
            </div>
            <div class="block">
                <button class="front-button button-gray">TRANSFORMER EN DEVIS</button>
            </div>
            <div class="block continue-purchase">
                <a href="#">Continuer mes achats</a>
            </div>

            <div class="block payment-options">Facilité de paiement</div>
            <div class="block block-capsule">
                <div class="capsule">
                    <div class="title">Besoin d'un conseil?</div>
                    <div class="text">
                        <div class="phone">+33 (0)2 47 52 66 01</div>
                        <div class="details">
                            (Contactez le service client du lundi au vendredi
                            de 09h00 à 12h30 et de 14h00 à 17h30)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div style="display: none" id="cart-templates">
        <div class="item" data-id="{-product_id-}">
            <div class="product-bar">
                <div class="image">
                    <img width="120" src="{-imageSmall-}"
                         alt="{-%label-}">
                </div>
                <div class="product-info">
                    <div class="label"><a href="{-uri_card_with_ref-}">{-label-}</a></div>
                    <div class="ref">Réf: {-ref-}</div>
                    <div class="attributes"></div>
                    <div class="stock-info {-stockClass-}">{-stockText-}</div>
                </div>
                <div class="quantity-container">
                    <div class="label">Quantité</div>
                    <div>
                        <input class="quantity-input" type="number" value="{-quantity-}">
                    </div>
                </div>
                <div class="price-container">
                    <?php if (true === $v['isB2B']): ?>
                        <div class="price-to-pay">{-linePrice-} <em>HT</em></div>
                    <?php else: ?>
                        <div class="price-with-tax">{-linePriceWithTax-} <em>TTC</em></div>
                        <div class="price-without-tax">{-linePriceWithoutTax-} <em>HT</em></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="action-bar">
                <a class="product-remove-btn" href="#">Supprimer</a>
                <a href="#">Ajouter aux favoris</a>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function (event) {
            $(document).ready(function () {
                var api = ekomApi.inst();
                var jCart = $('#cart');
                var jTpl = $('#cart-templates .item');
                var checkoutUrl = "<?php echo E::link("Ekom_checkoutOnePage"); ?>";

                var jProductList = $(".product-list", jCart);
                var jCartSummary = $(".cart-summary", jCart);

                var jCouponInput = jCart.find('.coupon-add-input');
                var jPromoSuccess = jCartSummary.find('.promo-message-success');
                var jPromoError = jCartSummary.find('.promo-message-error');
                var jCoupon = jCartSummary.find('.promo');
                var jCouponDetails = jCartSummary.find('.coupon-details');
                var jPromoSuccessMsg = jPromoSuccess.find('.promo-msg-success');
                var jPromoErrorMsg = jPromoError.find('.promo-msg-error');

                var jAttributes;


                function hideCouponMessage() {
                    jPromoSuccess.removeClass('visible');
                    jPromoError.removeClass('visible');
                }


                function updateCart(data) {

                    var items = data["items"];
                    jProductList.empty();
                    for (var i in items) {
                        var item = items[i];
                        var stockClass = "";
                        if ('stockAvailable' === item['stockType']) {
                            stockClass = "availability-in-stock";
                        } else if ('outOfStock' === item['stockType']) {
                            stockClass = "availability-out-of-stock";
                        }


                        var jClone = $.fn.cloneTemplate(jTpl, item);

                        jClone.find('.stock-info').addClass(stockClass);
                        jProductList.append(jClone);

                        jAttributes = jClone.find('.attributes');

                        for (var i in item['attributeDetails']) {
                            var attr = item['attributeDetails'][i];
                            var label = attr['label'];
                            var value = attr['value'];

                            var s = '<span class="attribute-name">' + label + '</span>: <span class="attribute-value">' + value + '</span>';
                            jAttributes.append(s);

                        }
                    }


                    jCartSummary.find(".total-with-tax").html(data["linesTotalWithTax"]);
                    jCartSummary.find(".tax-amount").html(data["taxAmount"]);
                    jCartSummary.find(".total-saving").html(data["totalSaving"]);
                    jCartSummary.find(".cart-total").html(data["cartTotal"]);
                    jCartSummary.find(".total-shipping-cost").html(data["estimatedTotalShippingCost"]);
                    jCartSummary.find(".order-grand-total").html(data["estimatedOrderGrandTotal"]);



                    jCartSummary.find('.coupon-details').remove();
                    if (true === data.hasCoupons) {


                        var jBase = jCoupon;
                        var c = 0;
                        for (var i in data.coupons) {
                            var coupon = data.coupons[i];
                            var s = '<tr class="coupon-details visible">' +
                                '<td>( ' + coupon.label + ' ' + coupon.saving + ' )</td>' +
                                '<td><a href="#" class="coupon-remove-btn" data-index="' + c + '">Supprimer</a></td>' +
                                '</tr>';
                            var jDetail = $(s);
                            jBase.after(jDetail);
                            jBase = jDetail;
                            c++;
                        }

                        jCoupon.addClass('visible');

                    }
                    else {
                        jCoupon.removeClass('visible');
                        jCouponDetails.removeClass('visible');
                    }


                    listenToQuantiyChanges();

                }


                function listenToQuantiyChanges() {


                    jCart.find(".quantity-input").on('change keyup', api.utils.debounce(function () {


                        if ($(this).val() < 0) {
                            $(this).val('0');
                        }

                        var productId = $(this).closest('.item').attr('data-id');
                        var newQty = $(this).val();


                        api.cart.updateItemQuantity(productId, newQty);


                    }, <?php echo LeeTheme::get("qtyInputDebounceTime", 250); ?>));
                }


                function addCoupon(code, force) {

                    api.cart.addCoupon(code, force, function (msg, type) {

                        if ('error' === type) {
                            jPromoErrorMsg.empty();
                            for (var i in msg) {
                                jPromoErrorMsg.append('<div><a class="coupon-message-close" href="#">X</a>' + msg[i] + '</div>');
                            }
                            jPromoError.addClass('visible');
                            jPromoSuccess.removeClass('visible');
                        }
                        else if ('confirm' === type) {
                            if (true === confirm(msg)) {
                                addCoupon(code, true);
                            }
                        }
                        else {
                            jPromoSuccessMsg.empty();
                            jPromoSuccessMsg.append('<div><a class="coupon-message-close" href="#">X</a>' + msg + '</div>');
                            jPromoSuccess.addClass('visible');
                            jPromoError.removeClass('visible');
                        }
                    });
                }


                api.on('cart.updated', function (data) {
                    updateCart(data);
                });


                jCart.on('click', function (e) {
                    var jTarget = $(e.target);
                    if (jTarget.hasClass("checkout-link")) {
                        window.location.href = checkoutUrl;
                        return false;
                    }
                    else if (jTarget.hasClass('coupon-add-btn')) {
                        var code = jCouponInput.val();

                        if (code.length > 0) {
                            addCoupon(code, false);
                        }
                        return false;
                    }
                    else if (jTarget.hasClass("coupon-remove-btn")) {
                        if (true === confirm("Are you sure you want to delete this coupon?")) {
                            var index = jTarget.attr('data-index');
                            hideCouponMessage();
                            api.cart.removeCoupon(index);
                        }
                        return false;
                    }
                    else if (jTarget.hasClass("coupon-message-close")) {
                        hideCouponMessage();
                        return false;
                    }
                    else if (jTarget.hasClass("product-remove-btn")) {
                        var productId = jTarget.closest('.item').attr('data-id');
                        api.cart.removeItem(productId);
                        return false;
                    }
                });


                listenToQuantiyChanges();


            });
        });
    </script>
<?php endif; ?>
