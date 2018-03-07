<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

$prefix = "/theme/" . ApplicationParameters::get("theme");
KamilleThemeHelper::css("product-bundles.css");


?>
<div class="window pt20 widget widget-product-bundles" id="widget-product-bundles">

    <div class="line top-title">
        <span class="main">PACKS</span>
    </div>
    <div style="clear: both"></div>


    <div class="bundles">
        <?php foreach ($v['bundles'] as $bId => $bundle): ?>
            <div class="bundle" data-id="<?php echo $bId; ?>">
                <div class="product-images">
                    <?php
                    $c = 0;
                    foreach ($bundle['items'] as $p): ?>
                        <?php if (0 !== $c++): ?>
                            <span class="plus">+</span>
                        <?php endif; ?>
                        <img data-id="<?php echo $p['product_id']; ?>" src="<?php echo $p['image']; ?>"
                             alt="<?php echo htmlspecialchars($p['label']); ?>">
                    <?php endforeach; ?>
                </div>
                <div class="payment-block">
                    <div class="price-info">
                        <?php if (false): ?>
                            <span class="price-with-tax line-through"><?php echo $bundle['totalSalePriceWithoutTax']; ?>
                                <abbr>HT</abbr></span>
                        <?php endif; ?>
                        <span class="price-without-tax sale-price"><?php echo $bundle['totalSalePriceWithTax']; ?>
                            <abbr>TTC</abbr></span>
                    </div>
                    <div class="description">
                        <span>Ce pack comprend:</span>
                        <ul class="product-description">
                            <?php foreach ($bundle['items'] as $p): ?>
                                <?php if (true === $p['isCurrentItem']): ?>
                                    <li><input data-id="<?php echo $p['product_id']; ?>"
                                               id="id-<?php echo $p['identifier']; ?>" type="checkbox" checked> <label
                                                for="id-<?php echo $p['identifier']; ?>"><?php echo $p['quantity'] . " " . $p['label']; ?>
                                            <?php if (true === $p['discountHasDiscount']): ?>
                                                <span class="price line-through"><?php echo $p['priceBase']; ?></span>
                                            <?php endif; ?>
                                            <span class="price"><?php echo $p['priceSale']; ?></span></label>
                                    </li>
                                <?php else: ?>
                                    <li><input data-id="<?php echo $p['product_id']; ?>" id="id1" type="checkbox"
                                               checked> <a
                                                href="<?php echo $p['uriCard']; ?>"><?php echo $p['quantity'] . " " . $p['label']; ?></a>
                                        <?php if (true === $p['discountHasDiscount']): ?>
                                            <span class="price line-through"><?php echo $p['priceBase']; ?></span>
                                        <?php endif; ?>
                                        <span class="price"><?php echo $p['priceSale']; ?></span>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <button class="buy-button front-button button-red">ACHETER</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


</div>

<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {
            var jWidget = $('#widget-product-bundles');


            var productId = <?php echo $v['product_id']; ?>;
            var api = ekomApi.inst();


            function hasPrevVisibleImage(jImg) {
                var jPrev = jImg;
                while (true) {
                    jPrev = jPrev.prev();
                    if (0 === jPrev.length) {
                        break;
                    }
                    if (jPrev.is('img')) {
                        if ('1' === jPrev.attr("data-visible")) {
                            return true;
                        }
                    }
                }
                return false;
            }

            api.on("bundle.updated", function (bundlesModel) {

                //----------------------------------------
                // refresh bundles model
                //----------------------------------------
                for (var bundleId in bundlesModel) {

                    var bundle = bundlesModel[bundleId];

                    var jBundle = jWidget.find('.bundle[data-id="' + bundleId + '"]');

                    var jBundleImages = jBundle.find('.product-images');

                    var items = bundle['items'];


                    var nbVisible = 0;

                    for (var i in items) {

                        var product = items[i];
                        var productId = product['product_id'];


                        var jImage = jBundleImages.find('[data-id="' + productId + '"]');

                        if (true === product['is_visible']) {
                            jImage.attr("data-visible", "1");
                            nbVisible++;
                        }
                        else {
                            jImage.attr("data-visible", "0");
                        }

                        jBundle.find(".price-without-tax").html(bundle['totalSalePriceWithoutTax']);
                        jBundle.find(".price-with-tax").html(bundle['totalSalePriceWithTax']);

                    }


                    // now remove unnecessary pluses
                    jBundle.find("img").each(function (i) {
                        var jPrevPlus;
                        if ('1' === $(this).attr("data-visible")) {
                            $(this).show();
                            jPrevPlus = $(this).prev('.plus');
                            if (hasPrevVisibleImage($(this))) {
                                if (jPrevPlus.length) {
                                    jPrevPlus.show();
                                }
                            }
                            else {
                                jPrevPlus.fadeOut();
                            }
                        }
                        else {
                            $(this).fadeOut();
                            jPrevPlus = $(this).prev('.plus');
                            if (jPrevPlus.length) {
                                jPrevPlus.fadeOut();
                            }
                        }
                    });

                }
            });


            function getRemoveProductIds() {
                var removeProductIds = {};
                jWidget.find('input[type=checkbox]').each(function () {
                    var bundleId = $(this).closest('.bundle').attr('data-id');
                    var _productId = $(this).attr('data-id');
                    var isChecked = $(this).is(':checked');
                    if (false === bundleId in removeProductIds) {
                        removeProductIds[bundleId] = [];
                    }
                    if (false === isChecked) {
                        removeProductIds[bundleId].push(_productId);
                    }
                });

                return removeProductIds;
            }

            function updateBundles() {
                var removeProductIds = getRemoveProductIds();
                api.bundle.getBundleModel(productId, removeProductIds);
            }


            jWidget.find('input[type=checkbox]').on('change', function () {
                updateBundles($(this));
                return false;
            });

            jWidget.on('click', function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass("buy-button")) {
                    var bundleId = jTarget.closest('.bundle').attr('data-id');
                    var removeProductIds = [];
                    var allRemoveProductIds = getRemoveProductIds();


                    if (bundleId in allRemoveProductIds) {
                        removeProductIds = allRemoveProductIds[bundleId];
                    }

                    api.bundle.addToCart(bundleId, removeProductIds);

                    return false;
                }
            });


        });
    });
</script>