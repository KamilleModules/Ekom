<?php

// product-box.md
use Bat\SessionTool;
use Bat\StringTool;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Theme\LeeTheme;


$uri = "/theme/" . ApplicationParameters::get("theme");


KamilleThemeHelper::css("product-box.css");

HtmlPageHelper::js($uri . "/libs/elevate-zoom/jquery.elevateZoom-3.0.8.min.js", null, null, false);
LeeTheme::useLib("slick");


$defaultSmallImageUri = "/modules/Ekom/img/no-image.jpg";
$defaultLargeImageUri = $defaultSmallImageUri;


if ($v['images']) {
    $defaultSmallImageUri = $v['images'][$v['defaultImage']]['medium'];
    $defaultLargeImageUri = $v['images'][$v['defaultImage']]['large'];
}


$maxQty = E::conf("stockShowQtyThreshold", 10);
$training = (array_key_exists('training', $v)) ? $v['training'] : '';
$productType = $v['product_type'];

$isFormation = ('training_product' === $productType);


/**
 * Above this threshold, we display attributes in a select list instead of a buttons list.
 */
$attrSwitchThreshold = 10;


?>
<style>
    .product-box .slick-prev,
    .product-box .slick-next {
        position: static;
        transform: none;
    }
</style>
<div class="widget widget-product-box product-box window" data-id="{product_id}">
    <div class="photos-nav">
        <div class="vertical-carousel" id="the-vertical-carousel">
            <?php foreach ($v['images'] as $fileName => $info):
                $thumb = $info['thumb'];
                ?>
                <div class="item">
                    <a href="#"><img data-id="<?php echo $fileName; ?>"
                                     src="<?php echo $thumb; ?>"></a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="video-container">
            <div class="item video-item">
                <a href="#"><img
                            src="<?php echo $uri . "/img/products/balance-board/thumb/balance-board-video.jpg"; ?>"></a>
            </div>
        </div>
    </div>
    <div class="product-viewbox">

        <div class="image-container">
            <!--            <img id="zoom_01" src='images/small/image1.png' data-zoom-image="images/large/image1.jpg"/>-->
            <img id="zoom01"
                 src="<?php echo $defaultSmallImageUri; ?>"
                 data-zoom-image="<?php echo $defaultLargeImageUri; ?>">
            <div class="video-wrapper">
                <video width="100%" height="100%">
                    <?php foreach ($v['video_sources'] as $url => $mimeType): ?>
                        <source src="<?php echo $url; ?>" type="<?php echo $mimeType; ?>">
                    <?php endforeach; ?>
                    <!--                <source src="movie.ogg" type="video/ogg">-->
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
        <div class="contact-us-banner">
            <span>Besoin d'un conseil ?</span>
            <span>+33 (0)2 47 52 66 01</span>
        </div>
        <div class="contact-us-info">
            (
            Contactez le service client du lundi au vendredi de 09h00 à 12h30
            et de 14h00 à 17h30
            )
        </div>
    </div>
    <div class="product-info">
        <div class="label">{label}</div>
        <div class="meta">
            <div class="reference">Réf. <span class="ref">{ref}</span></div>
            <?php
            if ($v['rating_nbVotes'] > 0):

                $n = ceil($v['rating_amount'] / 20); // 20 = 100 / 5stars
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
        </div>
        <div class="description">{description}</div>
        <div class="description-link">
            <a href="#">Fiche technique détaillée</a>
        </div>
        <?php
        $stockClass = "";

        switch ($v['stockType']) {
            case 'stockAvailable':
                $stockClass = "availability-in-stock";
                break;
            case 'outOfStock':
                $stockClass = "availability-out-of-stock";
                break;
            default:
                break;
        }


        $stockCountdownClass = "";
        if (-1 !== $v['quantity'] && $v['quantity'] <= $maxQty) {
            $stockCountdownClass = "visible";
        }

        ?>
        <div class="availability <?php echo $stockClass; ?>">
            {stockText}
        </div>
        <div class="availability-countdown <?php echo $stockCountdownClass; ?>">
            Plus que <span class="availability-countdown-quantity">{quantity}</span> exemplaire(s) en stock.<br>
            Passez vite votre commande !
        </div>

        <div>
            <?php if (true === $v['hasDiscount']): ?>
                <div class="price">
                    <span class="price-value original-price has-discount"><?php echo $v['price']; ?></span>
                    <em>TTC</em>
                </div>
            <?php endif; ?>
            <div class="price">
                <span class="price-value sale-price"><?php echo $v['salePrice']; ?></span> <em>TTC</em>
            </div>

        </div>
        <div class="pay-helpers">
            <a href="#">Facilités de paiement</a>
        </div>


        <div class="attribute-selectors">
            <?php foreach ($v['attributes'] as $name => $attr): ?>

                <div class="attribute-selector">
                    <div class="title"><?php echo $attr['label']; ?></div>

                    <?php if ('date' === $name || count($attr['values']) > $attrSwitchThreshold): ?>
                        <select>
                            <?php foreach ($attr['values'] as $item):

                                $sState = '';
                                $sSelected = '';
                                if ('1' === $item['selected']) {
                                    $sSelected .= 'selected="selected"';
                                }
                                ?>
                                <option <?php echo $sSelected . " " . $sState; ?>
                                        data-ajax="<?php echo htmlspecialchars($item['getProductInfoAjaxUri']); ?>"
                                        class="attribute-item"><?php echo $item['value_label']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>

                        <ul>
                            <?php foreach ($attr['values'] as $item):

                                $sClass = '';
                                if ('1' === $item['selected']) {
                                    $sClass .= "active";
                                }

                                ?>
                                <li data-ajax="<?php echo htmlspecialchars($item['getProductInfoAjaxUri']); ?>"
                                    class="attribute-item <?php echo $sClass; ?>"><a class="attribute-item"
                                                                                     href="#"><?php echo $item['value_label']; ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach ?>

        </div>

        <div class="line f-start-end">
            <div class="quantity">
                <div class="title">Quantité</div>
                <input class="quantity-input" type="number" value="1">
            </div>
            <div class="add-to-bookmarks">
                <a class="bookmarks add-to-bookmarks" href="#">Ajouter à ma liste</a>
            </div>
        </div>

        <div class="line">
            <button class="front-button button-black add-to-estimate">Ajouter au devis</button>
            <?php
            $sInactive = "";
            if ($isFormation) {
                $sInactive = 'disabled';
            }
            ?>
            <button class="front-button button-red add-to-cart-btn <?php echo $sInactive; ?>">Ajouter au panier</button>
        </div>
        <div class="connect">
            <a href="#">Identifiez-vous pour bénéficier du prix professionnel</a>
        </div>


    </div>

</div>


<script>


    var images = <?php echo json_encode($v['images']); ?>;

    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {


            var api = ekomApi.inst();
            var virtualQty = <?php echo $v['quantity']; ?>;
            var maxQty = <?php echo $maxQty; ?>;
            var isFormation = <?php echo (true === $isFormation) ? 'true' : 'false'; ?>;
            var jTrainingProductDateRangeSelector;
            var trainings;

            var jBox = $('.product-box');
            var jStockCountdown = $('.availability-countdown', jBox);
            var jStockCountdownQty = $('.availability-countdown-quantity', jBox);
            var jQtyInput = $('.quantity-input', jBox);

            var jAttrSelector = jBox.find(".attribute-selectors");
            var attrSwitchThreshold = <?php echo $attrSwitchThreshold; ?>;


            $('#the-vertical-carousel').slick({
                autoplay: false,
                arrows: true,
                vertical: true,
                draggable: false,
                slidesToShow: 4
            });

            var jZoom1 = $('#zoom01');
            jZoom1.elevateZoom();
            var jVideoWrapper = $(".video-wrapper");


            var video = null;

            function stopVideo() {
                if (null !== video) {
                    video.pause();
                    video.currentTime = 0;
                }
            }


            function processAttribute(jTarget) {
                var ajaxUri = jTarget.attr("data-ajax");
                if (false === jTarget.hasClass('disabled')) {
                    $.post(ajaxUri, function (model) {
                        updateBoxByProductBoxModel(jTarget, model);
                    }, 'json');
                }
            }


            function prepareAttributesSelectors() {
                jAttrSelector.find('select').each(function () {
                    $(this).off('change').on('change', function () {
                        var jOption = $(this).find(':selected');
                        processAttribute(jOption);
                    });
                });
            }

            function updateBoxByProductBoxModel(jTarget, model) {

                jBox.attr('data-id', model.product_id);
                jBox.find('.original-price').html(model.price);
                jBox.find('.sale-price').html(model.salePrice);
                jBox.find('.label').html(model.label);
                jBox.find('.description').html(model.description);
                jBox.find('.ref').html(model.ref);


                var jAvail = jBox.find('.availability');
                jAvail.html(model.stockText);

                var stockType = model.stockType;
                if ("stockAvailable" === stockType) {
                    jAvail.addClass("availability-in-stock").removeClass("availability-out-of-stock");
                }
                else {
                    jAvail.removeClass("availability-in-stock").addClass("availability-out-of-stock");
                }


                //----------------------------------------
                // attributes
                //----------------------------------------
                // set the active state on the clicked attribute value
                jAttrSelector.empty();
                var attributes = model["attributes"];
                for (var name in attributes) {
                    var attr = attributes[name];
                    var sAttr = '' +
                        '<div class="attribute-selector">' +
                        '<div class="title">' + attr['label'] + '</div>';

                    if ('date' === name || attr.values.length > attrSwitchThreshold) {
                        sAttr += '<select>';
                        for (var j in attr.values) {
                            var item = attr.values[j];
                            var sState = '';
                            var sSelected = '';
                            if ('1' === item["selected"]) {
                                sSelected = 'selected="selected"';
                            }


                            sAttr +=
                                '<option ' +
                                sSelected + ' ' + sState + ' ' +
                                'data-ajax="' + item['getProductInfoAjaxUri'] + '" ' +
                                'class="attribute-item">' +
                                item['value_label'] +
                                '</option>';
                        }
                        sAttr += '</select>';
                    }
                    else {


                        sAttr += '<ul>';
                        for (var j in attr.values) {
                            var item = attr.values[j];
                            var sClass = '';
                            if ('1' === item["selected"]) {
                                sClass = "active";
                            }

                            sAttr +=
                                '<li data-ajax="' + item['getProductInfoAjaxUri'] + '"' +
                                'class="attribute-item ' + sClass + '"><a class="attribute-item"' +
                                'href="#">' + item['value_label'] + '</a>' +
                                '</li>';
                        }
                        sAttr += '</ul>';
                    }

                    sAttr += '</div>';

                    jAttrSelector.append(sAttr);
                }


                var jImgCarousel = jBox.find('.vertical-carousel');

                var atLeastOneImage = false;
                images = model.images;
                jImgCarousel.empty();
                for (var fileName in model.images) {
                    var info = model.images[fileName];
                    var thumb = info.thumb;
                    var medium = info.medium;
                    var large = info.large;
                    var s = '<div class="item"><a href="#"><img data-id="' + fileName + '"src="' + thumb + '"></a></div>';
                    jImgCarousel.append(s);

                    atLeastOneImage = true;
                }


                if (true === atLeastOneImage) {
                    setTimeout(function () {
                        jImgCarousel.find("img:first").trigger('click');
                    }, 0);
                }


                quantity = model.quantity;
                jStockCountdownQty.html(model.quantity);
                if (quantity <= maxQty) {
                    jStockCountdown.addClass("visible");
                }
                else {
                    jStockCountdown.removeClass("visible");
                }
                updateQuantity();
                prepareAttributesSelectors();
            }


            //----------------------------------------
            // QUANTITY HANDLER
            //----------------------------------------
            function updateQuantity() {

                var val = jQtyInput.val();
                if (val < 0) {
                    val = 0;
                }
                else if (val > virtualQty && -1 !== virtualQty) { // -1 means infinite product
                    val = virtualQty;
                }
                jQtyInput.val(val);
            }


            jBox.find(".quantity-input").on('change keyup', function () {
                updateQuantity();
            });





            //----------------------------------------
            // CLICK HANDLER
            //----------------------------------------
            jBox.on('click', function (e) {


                var jTarget = $(e.target);

                if (jTarget.hasClass("disabled")) {
                    return false;
                }
                else if (jTarget.hasClass("attribute-item")) {

                    var jTarget = jTarget.closest('li');
                    processAttribute(jTarget);

                    return false;
                }
                else if (jTarget.closest('.video-wrapper').length) {

                    if (null !== video) {

                        if (video.paused) {
                            video.play();
                        }
                        else {
                            video.pause();
                        }
                    }
                    return false;
                }

                else if (jTarget.is('img')) {

                    var jClosest = jTarget.closest(".item");
                    if (jClosest.length > 0) {


                        if (jClosest.hasClass('video-item')) {
                            jVideoWrapper.show();

                            if (null === video) {
                                video = jVideoWrapper.find("video")[0];
                                video.play();
                            }
                            else {
                                if (video.paused) {
                                    video.play();
                                }
                                else {
                                    stopVideo();
                                }
                            }


                        }
                        else {
                            stopVideo();
                            jVideoWrapper.hide();


                            var itemName = jTarget.attr('data-id');

                            var smallUri = images[itemName]['medium'];
                            var largeUri = images[itemName]['large'];

                            jZoom1.attr('src', smallUri);
                            jZoom1.attr('data-zoom-image', largeUri);
                            jZoom1.data('zoom-image', largeUri).elevateZoom();

                        }
                        return false;
                    }
                }
                else if (jTarget.hasClass("add-to-cart-btn")) {

                    var productId = jBox.attr("data-id");
                    var qty = jBox.find(".quantity-input").val();

                    var complementaryId = 0;
                    if (isFormation) {
                        complementaryId = jTrainingProductDateRangeSelector.val();
                    }

                    api.cart.addItem(productId, qty, {
                        complementaryId: complementaryId
                    });
                    return false;
                }
                else if (jTarget.hasClass("add-to-estimate")) {

                    var productId = jBox.attr("data-id");
                    var qty = jBox.find(".quantity-input").val();

                    var complementaryId = 0;
                    if (isFormation) {
                        complementaryId = jTrainingProductDateRangeSelector.val();
                    }

                    api.ekomEstimateJsApi.cart.addItemToEstimate(productId, qty, {
                        complementaryId: complementaryId
                    });
                    return false;
                }
                else if (jTarget.hasClass("add-to-bookmarks")) {

                    var productId = jBox.attr("data-id");
                    api.user.addProductToWishlist(productId);
                    return false;
                }
                else {
//                    console.log(jTarget);
                }
            });

            updateQuantity();
            prepareAttributesSelectors();

        });
    });
</script>