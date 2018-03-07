<?php


namespace Theme\Lee\Ekom\ProductBox;

use Bat\StringTool;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Utils\E;
use Module\EkomProductCardVideo\View\EkomProductCardVideoViewHelper;
use Module\ThisApp\ThisAppConfig;
use Theme\Lee\Ekom\ProductBox\Helper\BionicDetailsHelper;
use Theme\LeeTheme;


class ProductBoxRenderer
{

    protected $textDescriptionLink;
    protected $linkDescriptionLink;
    protected $styleDescriptionLink;
    protected $positionRenderStock;
    protected $cssWidgetClass;
    protected $useDomContentLoaded;
    protected $showDescriptionLink;
    protected $useZoom;
    protected $useExpressPurchaseButton;
    protected $addToCartBtnExtraClass;
    protected $addToEstimateBtnExtraClass;


    private $boxCssId;


    public function __construct()
    {
        $this->linkDescriptionLink = "#widget-product-features";
        $this->textDescriptionLink = "Fiche technique détaillée";
        $this->styleDescriptionLink = "";
        $this->positionRenderStock = 1;
        $this->cssWidgetClass = "";
        $this->showDescriptionLink = true;
        $this->useDomContentLoaded = true;
        $this->useZoom = true;
        $this->useExpressPurchaseButton = false;
        $this->addToCartBtnExtraClass = '';
        $this->addToEstimateBtnExtraClass = '';
    }


    public static function create()
    {
        return new static();
    }





    //--------------------------------------------
    //
    //--------------------------------------------
    public function renderErroneousBox(array $model)
    {
        ?>
        <div class="window central-statement">
            <div>
                <div>
                    Oops, une erreur est survenue.
                </div>
                <h4><?php echo $model['errorTitle']; ?></h4>
                <p><?php echo $model['errorMessage']; ?></p>
            </div>
        </div>
        <?php
    }

    public function render(array $model)
    {
        $v = $model;
        $boxCssId = $this->getBoxCssId();


        // product-box.md
        KamilleThemeHelper::css("product-box.css");

        if (true === $this->useZoom) {
            LeeTheme::useLib("elevateZoom");
        }
        LeeTheme::useLib("slick");
        LeeTheme::useLib("prettyInputNumber");
        LeeTheme::useLib("simpleselect");


        $defaultSmallImageUri = "/modules/Ekom/img/no-image.jpg";
        $defaultLargeImageUri = $defaultSmallImageUri;
        $uriCheckoutPage = E::link("Ekom_checkoutOnePage");


        // nb max of images to show
        $slidesToShow = 4;

        if ($v['images']) {
            $defaultSmallImageUri = $v['images'][$v['defaultImage']]['medium'];
            $defaultLargeImageUri = $v['images'][$v['defaultImage']]['large'];
        }


        $alertQtyThreshold = 10;
        $training = (array_key_exists('training', $v)) ? $v['training'] : '';
        $productType = $v['product_type'];

        $isFormation = ('training_product' === $productType);


        /**
         * Above this threshold, we display attributes in a select list instead of a buttons list.
         */
        $attrSwitchThreshold = 10;


        //--------------------------------------------
        // EkomProductCardVideo module
        //--------------------------------------------
        $videoInfo = $v['video_info'];
        list($videoImages, $videoSources) = EkomProductCardVideoViewHelper::getAllVideoInfoToList($videoInfo);
//        az($videoInfo, $videoImages, $videoSources);


        ?>
        <style>
            .product-box .slick-prev,
            .product-box .slick-next {
                position: static;
                transform: none;
            }
        </style>
        <div
                id="<?php echo $boxCssId; ?>"
                class="widget widget-product-box product-box window <?php echo $this->cssWidgetClass; ?>"
                data-id="<?php echo $v['product_id']; ?>"
        >
            <div class="photos-nav">
                <div class="vertical-carousel the-vertical-carousel">
                    <?php foreach ($v['images'] as $fileName => $info):
                        $thumb = $info['thumb'];
                        ?>
                        <div class="item">
                            <a href="#"><img data-id="<?php echo $fileName; ?>"
                                             src="<?php echo $thumb; ?>"></a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($videoImages): ?>
                    <div class="video-container">
                        <?php foreach ($videoImages as $fileName => $uriThumb): ?>
                            <div class="item video-item" data-id="<?php echo $fileName; ?>">
                                <a href="#"><img
                                            src="<?php echo $uriThumb; ?>"></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="product-viewbox">

                <div class="image-container">
                    <!--            <img id="zoom_01" src='images/small/image1.png' data-zoom-image="images/large/image1.jpg"/>-->
                    <img class="zoom01"
                         src="<?php echo $defaultSmallImageUri; ?>"
                         data-zoom-image="<?php echo $defaultLargeImageUri; ?>">
                    <div class="video-wrapper">
                        <?php foreach ($videoSources as $fileName => $sources): ?>
                            <video width="100%" height="100%" data-id="<?php echo $fileName; ?>">
                                <?php foreach ($sources as $url => $mimeType): ?>
                                    <source src="<?php echo $url; ?>" type="<?php echo $mimeType; ?>">
                                <?php endforeach; ?>
                                <!--                <source src="movie.ogg" type="video/ogg">-->
                                Your browser does not support the video tag.
                            </video>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="contact-us-banner t-next">
                    <span class="f-auto t-nowrap">Besoin d'un conseil ?</span>
                    <span class="t-nowrap">+33 (0)2 47 52 66 01</span>
                </div>
                <div class="contact-us-info">
                    (Contactez le service client
                    du lundi au vendredi
                    de 09h00 à 12h30 et de 14h00 à 17h30)
                </div>
            </div>
            <div class="product-info">

                <div class="label"><?php echo $v['label']; ?></div>
                <div class="meta">
                    <div class="reference">Réf. <span class="ref"><?php echo $v['ref']; ?>
                            <?php $this->renderRefDebugString($v); ?>
                            </span></div>
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
                                (<?php echo $v['rating_nbVotes']; ?> avis)
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="description"><?php echo $v['description']; ?></div>
                <?php if ($this->showDescriptionLink): ?>
                    <div class="description-link" <?php echo $this->styleDescriptionLink; ?>>
                        <a href="<?php echo $this->linkDescriptionLink; ?>"
                           class="navigate-feature-link"><?php echo $this->textDescriptionLink; ?></a>
                    </div>
                <?php endif; ?>
                <?php


                if (1 === $this->positionRenderStock) {
                    $this->renderStock($v);
                }


                if (true === $v['taxHasTax']) {
                    $priceType = "TTC";
                } else {
                    $priceType = "HT";
                }
                $priceType = '<em class="' . strtolower($priceType) . '">' . $priceType . '</em>';
                ?>


                <div>
                    <?php if (true === $v['discountHasDiscount']): ?>
                        <div class="price">
                            <span class="price-value original-price has-discount"><?php echo $v['priceOriginal']; ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="price">
                        <span class="price-value sale-price"><?php echo $v['priceSale']; ?></span>
                        <?php echo $priceType; ?>
                    </div>

                </div>

                <?php $this->renderLineBelowPrice($v); ?>
                <?php $this->renderBlocBeforeAttributes($v); ?>


                <div class="attribute-selectors cell-items-container">
                    <?php foreach ($v['attributes'] as $name => $attr): ?>

                        <div class="attribute-selector cell-items">
                            <div class="title"><?php echo $attr['label']; ?></div>

                            <?php if ('date' === $name || count($attr['values']) > $attrSwitchThreshold): ?>
                                <select class="s-simple-select bionic-select"
                                        data-action="product.getInfo"
                                        data-param-product_id="$this"
                                >
                                    <?php foreach ($attr['values'] as $item):

                                        $sState = '';
                                        $sSelected = '';
                                        if ('1' === $item['selected']) {
                                            $sSelected .= 'selected="selected"';
                                        }
                                        ?>
                                        <option <?php echo $sSelected . " " . $sState; ?>
                                                value="<?php echo $item['product_id']; ?>"
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
                                            class="bionic-btn attribute-item <?php echo $sClass; ?>"
                                            data-action="product.getInfo"
                                            data-param-product_id="<?php echo $item['product_id']; ?>"
                                        ><a href="#"><?php echo $item['value_label']; ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endforeach ?>

                </div>


                <?php
                $this->renderBelowAttributes($v);
                ?>



                <?php
                if (2 === $this->positionRenderStock) {
                    $this->renderStock($v);
                }


                $this->renderQuantityLine($v);

                ?>


                <div class="points-info">
                    Cumulez <b>20 points</b> pour l’achat de ce produit
                </div>


                <?php

                $classDisabled = (false === $v['quantityInStock']) ? 'disabled' : '';
                $purchaseClass = (true === $this->useExpressPurchaseButton) ? "popup" : "";

                ?>
                <div class="line-actions <?php echo $purchaseClass; ?>">

                    <table style="position: relative;left: -4px;">
                        <tr>
                            <td>
                                <button
                                        class="lee-gray-button add-to-estimate <?php echo $classDisabled; ?>
                                        <?php echo $this->addToEstimateBtnExtraClass; ?> bionic-btn"
                                        data-action="estimateCart.addItem"
                                        data-param-quantity="$quantity"
                                        data-param-product_id="<?php echo $v['product_id']; ?>"
                                    <?php BionicDetailsHelper::renderBionicDetailsMap($v['productDetailsMap']); ?>
                                >Ajouter
                                    au devis
                                </button>
                            </td>
                            <td>
                                <?php
                                $sInactive = "";
                                if ($isFormation) {
                                    $sInactive = 'disabled';
                                }
                                ?>
                                <button class="lee-red-button bionic-btn
                                <?php echo $this->addToCartBtnExtraClass; ?>
                                add-to-cart-btn <?php echo $sInactive; ?> <?php echo $classDisabled; ?>"
                                        data-action="cart.addItem"
                                        data-param-quantity="$quantity"
                                        data-param-product_id="<?php echo $v['product_id']; ?>"
                                    <?php BionicDetailsHelper::renderBionicDetailsMap($v['productDetailsMap']); ?>
                                >
                                    Ajouter au panier
                                </button>
                            </td>
                            <?php if (true === $this->useExpressPurchaseButton): ?>
                                <td>
                                    <button class="lee-black-button purchase-express-btn <?php echo $sInactive; ?> <?php echo $classDisabled; ?>">
                                        Achat express
                                    </button>
                                </td>
                            <?php endif; ?>
                        </tr>

                    </table>
                </div>

                <?php $this->renderBelowPaymentButtons($v); ?>


            </div>

        </div>


        <script>


            window.jqueryComponent.ready(function () {


                var jBox;
                var images = <?php echo json_encode($v['images']); ?>;


                var api = ekomApi.inst();


                jBox = $('#<?php echo $boxCssId; ?>');
//                    var jAttrSelector = jBox.find(".attribute-selectors");


                var jZoom1 = $('.zoom01', jBox);
                var jVideoWrapper = $(".video-wrapper", jBox);

                $('.the-vertical-carousel', jBox).slick({
                    autoplay: false,
                    arrows: true,
                    vertical: true,
                    draggable: false,
                    slidesToShow: <?php echo $slidesToShow; ?>
                });

                <?php if(true === $this->useZoom): ?>
                jZoom1.elevateZoom();
                <?php endif; ?>

                var video = null;

                function stopVideo() {
                    if (null !== video) {
                        video.pause();
                        video.currentTime = 0;
                    }
                }

                function nowPlayThisVideo(fileName) {
                    stopVideo();
                    jVideoWrapper.show();
                    var jVideo = jVideoWrapper.find('video[data-id="' + fileName + '"]');
                    jVideoWrapper.find('video').removeClass("active");
                    jVideo.addClass('active');
                    video = jVideo[0];
                    video.play();
                }


                //----------------------------------------
                // CLICK HANDLER
                //----------------------------------------
                jBox.off('click.productBoxRenderer').on('click.productBoxRenderer', function (e) {


                    var jTarget = $(e.target);

                    if (jTarget.hasClass("disabled")) {
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



                            /**
                             * activate a video when clicking on a thumbnail
                             */
                            if (jClosest.hasClass('video-item')) {
                                var fileName = jClosest.attr("data-id");
                                nowPlayThisVideo(fileName);
                            }
                            else {
                                stopVideo();
                                jVideoWrapper.hide();

                                var itemName = jTarget.attr('data-id');

                                var smallUri = images[itemName]['medium'];
                                var largeUri = images[itemName]['large'];


                                jZoom1.attr('src', smallUri);
                                <?php if(true === $this->useZoom): ?>
                                jZoom1.attr('data-zoom-image', largeUri);
                                jZoom1.data('zoom-image', largeUri).elevateZoom();
                                <?php endif; ?>

                            }
                            return false;
                        }
                    }
//
//                    else if (jTarget.hasClass("add-to-bookmarks")) {
//
//                        var productId = jBox.attr("data-id");
//                        var productDetails = {};
//                        api.trigger("productBox.collectDetails", productDetails);
//                        api.user.addProductToWishlist(productId, productDetails);
//                        return false;
//                    }
                    else if (jTarget.hasClass("navigate-feature-link")) {
                        var hash = jTarget.attr("href");
                        api.trigger("scrollToFeatureBarItem", hash);
                        return false;
                    }
                });


                jBox.find('.s-simple-select').simpleselect();
                window.prettyInputNumber();


                api.trigger('productBox.ready');


            });
        </script>

        <?php
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function renderRefDebugString(array $box)
    {
        ?>
        (#<?php echo $box['product_id']; ?>
        -<?php echo $box['card_id']; ?>)
        <?php
    }


    /**
     * Recap of how this "quantity" system works:
     *
     * There are different elements to take into account:
     *
     * - the cart quantity
     *          imagine you have 50 products left, but then
     *          if you have already 10 of that product in your cart,
     *          so there is really only 40 products left.
     *
     *          This number (40) is saved as virtual_qty,
     *          and represent the available qty for the current user,
     *          considering the products in her cart.
     *
     *
     * - when an attribute changes, we get the following relevant info from the model:
     *      - qty: int
     *      - is_in_stock: bool
     *
     *
     *
     * - when an attribute is selected,
     *      the input should contain either the number 1 (if the product is available),
     *      or 0 if the product is not available
     *
     *
     * In this current implementation:
     *
     * - when an attribute changes (and/or when the page is first displayed):
     *      we have only two possibilities:
     *      either the product is available, or it's not (qty=0).
     *
     *      If the product is available, we display the "is available block",
     *      and if not available, we display the "not available block".
     *
     *      Our technique is to display both blocks at the same time,
     *      and switch the visibility of one or the other depending
     *      on the qty.
     *
     *      Inside the "is available block", we have a number representing the virtual quantity.
     *      This number is updated as cart quantities are updated.
     *
     *
     */
    protected function renderStock(array $model)
    {
        $v = $model;


        $quantity = $v['quantityStock'];
        $outOfStockText = $v['outOfStockText'];
        if (empty($stockText)) {
            $outOfStockText = "Ce produit n'est plus en stock, réapprovisionnement en cours";
        }
        $isAvailable = (0 !== $quantity);
        $this->renderStockText($isAvailable, $quantity, $outOfStockText);
    }


    protected function renderStockText($isAvailable, $quantity, $outOfStockText)
    {

        ?>
        <div style="margin-top:10px" class="availability-container availability-<?php echo (int)$isAvailable; ?>">
            <div class="availability availability-in-stock">
                En stock
                <span style="display: none;">
                (<span
                            class="virtual-quantity-number"><?php echo $quantity; ?></span>
                exemplaires disponibles)
                    </span>
            </div>
            <div class="availability availability-out-of-stock">
                <?php echo $outOfStockText; ?>
            </div>
        </div>
        <?php
    }


    protected function renderLineBelowPrice(array $model)
    {
        ?>
        <div class="pay-helpers">
            <a href="#">Facilités de paiement</a>
        </div>
        <?php
    }


    protected function renderBlocBeforeAttributes(array $model)
    {

    }


    protected function renderQuantityLine(array $model)
    {

        $qty = (true === $model['quantityInStock']) ? '1' : '0';

        ?>
        <div class="line f-start-end">
            <div class="quantity">
                <div class="title">Quantité</div>
                <div class="pretty-input-number">
                    <input type="number" class="quantity-input bionic-target" data-id="quantity"
                           value="<?php echo $qty; ?>">
                </div>
            </div>
            <div class="add-to-bookmarks">
                <a class="bookmarks add-to-bookmarks bionic-btn"
                   href="#"
                   data-action="user.addProductToWishlist"
                   data-param-product_id="<?php echo $model['product_id']; ?>"
                    <?php BionicDetailsHelper::renderBionicDetailsMap($model['productDetailsMap']); ?>
                >Ajouter à ma liste</a>
            </div>
        </div>
        <?php
    }


    protected function renderBelowPaymentButtons(array $model)
    {
        ?>
        <div class="connect">
            <a href="<?php echo $model['uriLogin']; ?>">Identifiez-vous pour bénéficier du prix professionnel</a>
        </div>
        <?php
    }

    protected function renderBelowAttributes(array $model)
    {

    }

    protected function getBoxCssId()
    {
        if (null === $this->boxCssId) {
            $this->boxCssId = StringTool::getUniqueCssId('box-');
        }
        return $this->boxCssId;
    }

}