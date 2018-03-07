<?php

// product-box.md
use Bat\StringTool;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Theme\LeeTheme;


//--------------------------------------------
// DISCLAIMER: THIS TEMPLATE IS A DEPRECATED WORK IN PROGRESS
//--------------------------------------------
/**
 * It was fun, the goal being to update the stock quantity while the user increments the quantity input,
 * unfortunately after a second thought this makes no/little sense and shouldn't be implemented in the first place,
 * I keep this code just in case, but I'm not going to use it (unless I'm told to).
 *
 * By the way, the code is not fully functional but the main idea is there.
 *
 *
 * Note: if you want to follow on this stupid idea, be aware that you have to take the mincart  into account too.
 *
 */


$uri = "/theme/" . ApplicationParameters::get("theme");


KamilleThemeHelper::css("product-box.css");
HtmlPageHelper::js($uri . "/libs/elevate-zoom/jquery.elevateZoom-3.0.8.min.js", null, null, false);
LeeTheme::useLib("slick");


$defaultSmallImageUri = $v['images'][$v['defaultImage']]['medium'];
$defaultLargeImageUri = $v['images'][$v['defaultImage']]['large'];


/**
 * In this template, we use an advanced technique for maintaining
 * quantities up to date and synced with the cart.
 *
 * Basically, our goal is to display a quantity which takes
 * the cart quantities into account.
 *
 * The so-called virtualQuantity is the stockQuantity (aka quantity) minus the cart quantity.
 * We can access the cart quantities from the php session, using the ekomApi's cartLayer layer.
 */
$id2Quantity = EkomApi::inst()->cartLayer()->getCartProduct2Quantities();

$maxQty = E::conf("stockShowQtyThreshold", 10);


?>
<style>
    .product-box .slick-prev,
    .product-box .slick-next {
        position: static;
        transform: none;
    }
</style>
<div class="product-box window" data-id="{product_id}">
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
            <?php if (array_key_exists("rating_amount", $v)):

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
        if ($v['quantity'] <= $maxQty) {
            $stockCountdownClass = "visible";
        }

        ?>
        <div class="availability <?php echo $stockClass; ?>">
            {stockText}
        </div>
        <div class="availability-countdown <?php echo $stockCountdownClass; ?>">
            Plus que <span class="availability-countdown-quantity">{quantity}</span> exemplaire(s) en stock
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

            <?php endforeach ?>
            <div class="attribute-selector">
                <div class="title"><?php echo StringTool::ucfirst($attr['label']); ?></div>
                <ul>
                    <?php foreach ($attr['values'] as $item):
                        extract($item); // $value, $selected, $active, $quantity, $existence,  $productUri, $getProductInfoAjaxUri, $product_id


                        $sClass = '';
                        if ('1' === $selected) {
                            $sClass .= "active";
                        }
                        if ('0' === $active || "0" === $existence || "0" === $quantity) {
                            $sClass .= " disabled";
                        }
                        ?>
                        <li data-ajax="<?php echo htmlspecialchars($getProductInfoAjaxUri); ?>"
                            class="attribute-item <?php echo $sClass; ?>"><a class="attribute-item"
                                                                             href="#"><?php echo $value; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </div>

        <div class="line f-start-end">
            <div class="quantity">
                <div class="title">Quantité</div>
                <input class="quantity-input" type="number" value="1">
            </div>
            <div class="add-to-bookmarks">
                <a class="bookmarks" href="#">Ajouter à ma liste</a>
            </div>
        </div>

        <div class="line">
            <button class="front-button button-black add-to-estimate">Ajouter au devis</button>
            <button class="front-button button-red add-to-cart-btn">Ajouter au panier</button>
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
            var stockQty = <?php echo $v['quantity']; ?>;
            var productId = <?php echo $v['product_id']; ?>;
            var product2Qty = <?php echo json_encode($id2Quantity); ?>;
            var maxQty = <?php echo $maxQty; ?>;
            var virtualQty = 0;


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


            var jBox = $('.product-box');
            var jStockCountdown = $('.availability-countdown', jBox);
            var jStockCountdownQty = $('.availability-countdown-quantity', jBox);
            var jQtyInput = $('.quantity-input', jBox);


            function refreshVirtualQuantity(pId, stockQuantity) {
                var tmp = stockQuantity;
                if (pId in product2Qty) {
                    tmp -= product2Qty[pId];
                }
                if (tmp < 0) {
                    tmp = 0;
                }
                virtualQty = tmp;
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


                // set the active state on the clicked attribute value
                var jAttrContainer = jTarget.parent();
                jAttrContainer.find(".active").removeClass("active");
                jTarget.addClass("active");


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


                virtualQty = model.quantity;
                updateQuantity();


            }


            //----------------------------------------
            // QUANTITY HANDLER
            //----------------------------------------
            function updateQuantity() {

                var val = jQtyInput.val();
                if (val < 0) {
                    val = 0;
                }
                else if (val > virtualQty) {
                    val = virtualQty;
                }

                var remaining = virtualQty - val;
                if (remaining <= maxQty) {
                    jStockCountdown.addClass("visible");
                }
                else {
                    jStockCountdown.removeClass("visible");
                }


                jStockCountdownQty.html(remaining);

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

                if (jTarget.hasClass("attribute-item")) {
                    var jTarget = jTarget.closest('li');
                    var ajaxUri = jTarget.attr("data-ajax");
                    if (false === jTarget.hasClass('disabled')) {
                        $.post(ajaxUri, function (model) {

                            updateBoxByProductBoxModel(jTarget, model);
                        }, 'json');
                    }
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
                    api.cart.addItem(productId, qty);
                    return false;
                }
                else {
//                    console.log(jTarget);
                }
            });

            refreshVirtualQuantity(productId, stockQty);
            updateQuantity();

        });
    });
</script>