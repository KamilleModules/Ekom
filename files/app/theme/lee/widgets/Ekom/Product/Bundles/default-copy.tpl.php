<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

$prefix = "/theme/" . ApplicationParameters::get("theme");
KamilleThemeHelper::css("product-bundles.css");


?>
<div class="window2 pt20 widget widget-product-bundles" id="widget-product-bundles">

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
                        <span class="price-with-tax line-through"><?php echo $bundle['totalSalePriceWithoutTax']; ?>
                            <abbr>HT</abbr></span>
                        <span class="price-without-tax sale-price"><?php echo $bundle['totalSalePriceWithTax']; ?>
                            <abbr>TTC</abbr></span>
                    </div>
                    <div class="description">
                        <span>Ce pack comprend:</span>
                        <ul class="product-description">
                            <?php foreach ($bundle['items'] as $p): ?>
                                <?php if (true === $p['isCurrentItem']): ?>
                                    <li><input data-id="<?php echo $p['product_id']; ?>"
                                               id="id-<?php echo $p['product_id']; ?>" type="checkbox" checked> <label
                                                for="id-<?php echo $p['product_id']; ?>"><?php echo $p['quantity'] . " " . $p['label']; ?>
                                            <span class="price"><?php echo $p['salePrice']; ?></span></label>
                                    </li>
                                <?php else: ?>
                                    <li><input data-id="<?php echo $p['product_id']; ?>" id="id1" type="checkbox"
                                               checked> <a
                                                href="<?php echo $p['uriCard']; ?>"><?php echo $p['quantity'] . " " . $p['label']; ?></a>
                                        <span class="price"><?php echo $p['salePrice']; ?></span>
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

            api.on("bundle.updated", function (bundlesModel) {

                //----------------------------------------
                // refresh bundles model
                //----------------------------------------
                for (var bundleId in bundlesModel) {

                    var bundle = bundlesModel[bundleId];

                    var jBundle = jWidget.find('.bundle[data-id="' + bundleId + '"]');

                    var jBundleImages = jBundle.find('.product-images');

                    var items = bundle['items'];


                    var visibleIds = [];
                    var notVisibleIds = [];

                    for (var i in items) {

                        var product = items[i];
                        var productId = product['product_id'];


                        var jImage = jBundleImages.find('[data-id="' + productId + '"]');

                        if (true === product['is_visible']) {
                            jImage.show();
                            jImage.attr("data-visible", "1");
                            visibleIds.push(productId);

                        }
                        else {
                            jImage.fadeOut();
                            jImage.attr("data-visible", "0");
                            notVisibleIds.push(productId);
                        }

                        jBundle.find(".price-without-tax").html(bundle['totalSalePriceWithoutTax']);
                        jBundle.find(".price-with-tax").html(bundle['totalSalePriceWithTax']);

                    }


                    // now remove unnecessary pluses
                    var aImgs = jBundle.find("img").toArray();
                    for (var i in aImgs) {
                        var jImg = $(aImgs[i]);
                        var jNext = jImg.next();
                        if (jNext.length > 0) { // all but last image

                            if ('0' === jImg.attr('data-visible')) {
                                jImg.next('.plus').fadeOut();
                            }
                            else {

                                /**
                                 * If there is at least one visible image AFTER
                                 * the current image, then we display the next plus
                                 */
                                var nextIndex = i;
                                while (true) {
                                    nextIndex = parseInt(nextIndex) + 1;
                                    if (nextIndex in aImgs) {
                                        var jNextImg = $(aImgs[nextIndex]);
                                        if ('1' === jNextImg.attr("data-visible")) {
                                            jImg.next('.plus').show();
                                            break;
                                        }
                                    }
                                    else {
                                        break;
                                    }
                                }
                            }
                        }
                        else { // last image
                            if ('0' === jImg.attr('data-visible')) {
                                jImg.prev('.plus').fadeOut();

                            }
                            else {
                                /**
                                 * If there is at least one visible image BEFORE
                                 * the current image, then we display the previous plus
                                 */
                                var prevIndex = i;
                                while (true) {
                                    prevIndex = parseInt(prevIndex) - 1;
                                    if (prevIndex in aImgs) {
                                        var jPrevImg = $(aImgs[prevIndex]);
                                        if ('1' === jPrevImg.attr("data-visible")) {
                                            jImg.prev('.plus').show();
                                            break;
                                        }
                                    }
                                    else {
                                        break;
                                    }
                                }

                            }
                        }
                    }
                }
            });


            function updateBundles() {
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


                api.bundle.getBundleModel(productId, removeProductIds);
            }


            jWidget.find('input[type=checkbox]').on('change', function () {
                updateBundles();
                return false;
            });
        });
    })
    ;
</script>