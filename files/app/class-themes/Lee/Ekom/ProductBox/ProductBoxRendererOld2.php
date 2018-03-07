<?php


namespace Theme\Lee\Ekom\ProductBox;

use Bat\StringTool;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Utils\E;
use Module\EkomProductCardVideo\View\EkomProductCardVideoViewHelper;
use Module\ThisApp\ThisAppConfig;
use Theme\LeeTheme;


class ProductBoxRendererOld2
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


        $nbImages = count($v['images']);
        $slidesToShow = $nbImages;

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
                data-id="{product_id}"
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

                <div class="label">{label}</div>
                <div class="meta">
                    <div class="reference">Réf. <span class="ref">{ref} (#<?php echo $v['product_id']; ?>
                            -<?php echo $v['card_id']; ?>)</span></div>
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
                            <span class="price-value original-price has-discount"><?php echo $v['priceBase']; ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="price">
                        <span class="price-value sale-price"><?php echo $v['priceSale']; ?></span>
                        <?php echo $priceType; ?>
                    </div>

                </div>

                <?php $this->renderLineBelowPrice($v); ?>
                <?php $this->renderBlocBeforeAttributes($v); ?>



                <?php
                /**
                 * @todo-ling: faire les select comme sur le visuel
                 * @todo-ling: faire les input (quantity) comme sur le visuel
                 */
                ?>
                <div class="attribute-selectors cell-items-container">
                    <?php foreach ($v['attributes'] as $name => $attr): ?>

                        <div class="attribute-selector cell-items">
                            <div class="title"><?php echo $attr['label']; ?></div>

                            <?php if ('date' === $name || count($attr['values']) > $attrSwitchThreshold): ?>
                                <select class="s-simple-select">
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
                                        <?php echo $this->addToEstimateBtnExtraClass; ?>">Ajouter
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
                                <button class="lee-red-button
                                <?php echo $this->addToCartBtnExtraClass; ?>
                                add-to-cart-btn <?php echo $sInactive; ?> <?php echo $classDisabled; ?>">
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


            var images = <?php echo json_encode($v['images']); ?>;

            <?php if(true === $this->useDomContentLoaded): ?>
            document.addEventListener("DOMContentLoaded", function (event) {
                <?php endif; ?>





                /**
                 * Dynamic equivalent of:  Theme\Lee\Ekom\ProductBox\Helper\AttributeContainerRenderer::renderAttributeContainer
                 * or Theme\Lee\Ekom\ProductBox\Helper\AttributeContainerRenderer::renderAttributeContainerAsSelect,
                 * depending on whether a select or an ul is found in the jContainer.
                 *
                 **/
                window.ekomProductBoxRefreshAttributeBoxes = function (attrBoxModel, jContainer) {

                    var jItemsContainer = jContainer.find('select');
                    if (jItemsContainer.length) {
                        jItemsContainer.empty();
                        for (var i in attrBoxModel) {
                            var item = attrBoxModel[i];

                            var sSel = '';
                            if (true === item.isSelected) {
                                sSel += ' selected="selected" ';
                            }


                            var s = '';
                            s += '<option value="' + item.value + '"' +
                                sSel +
                                'data-ajax="' + item.uriAjax + '" ' +
                                'class="attribute-item">' +
                                '<a class="refresh-trigger" href="#">' + item.label + '</a>';
                            '</option>';
                            jItemsContainer.append(s);
                        }
                    }
                    else {

                        jItemsContainer = jContainer.find('ul');
                        jItemsContainer.empty();
                        for (var i in attrBoxModel) {
                            var item = attrBoxModel[i];


                            var sClass = '';
                            if (true === item.isSelected) {
                                sClass += 'active';
                            }


                            var s = '';
                            s += '<li data-ajax="' + item.uriAjax + '" ' +
                                'data-key="' + item.value + '" ' +
                                'class="refresh-trigger ' + sClass + '">' +
                                '<a class="refresh-trigger" href="#">' + item.label + '</a>' +
                                '</li>';
                            jItemsContainer.append(s);
                        }
                    }
                };


                /**
                 * This mechanism is thought for modules.
                 * If a module wants to invoke the "productBox page ajax refresher",
                 * they can simply add the refresh-trigger class to an element,
                 * and the data-ajax attribute with the desired uri as the value.
                 *
                 **/
                function processRefreshTrigger(jTarget, _jBox) {
                    var ajaxUri = jTarget.attr("data-ajax");
                    $.post(ajaxUri, function (model) {
                        updateBoxByProductBoxModel(jTarget, model, _jBox);
                    }, 'json');
                }


                function prepareRefreshTriggers(_jBox) {

                    var _jAttrSelector = _jBox.find(".cell-items-container");
                    _jAttrSelector.find('.s-simple-select').each(function () {
                        $(this).off('change').on('change', function () {
                            var jOption = $(this).find(':selected');
                            processRefreshTrigger(jOption, _jBox);
                        });


                    });
                }


                function _addPriceType(jPriceContainer, model) {
                    jPriceContainer.html(model.salePrice);
                    if (true === model.taxApplies) {
                        var jPriceType = jPriceContainer.closest(".price").find("em");
                        if (0 === jPriceType.length) {
                            jPriceType = $('<em></em>');
                            jPriceContainer.closest(".price").append(jPriceType);
                        }
                        var priceType = "TTC";
                        if (true === model.isB2B) {
                            priceType = "HT";
                        }
                        jPriceType.html(priceType);
                    }
                    else {
                        jPriceContainer.closest(".price").find("em").remove();
                    }
                }

                function updateBoxByProductBoxModel(jTarget, model, _jBox) {

                    var _jAttrSelector = _jBox.find(".attribute-selectors");
//                        api.trigger('gui.onProductBoxUpdatedBefore', model);

                    //----------------------------------------
                    // QUANTITY
                    //----------------------------------------
                    var quantity = model.quantityStock;
                    window.ekomUpdateProductBoxQuantity(quantity, _jBox);

                    //----------------------------------------
                    // PRODUCT PAGE VARIOUS THINGS
                    //----------------------------------------
                    var ref = model.ref;
                    if (true) { // debug
                        ref += ' (#' + model.product_id + '-' + model.card_id + ')';
                    }


                    var jOriginalPrice = _jBox.find('.original-price');
                    var jSalePrice = _jBox.find('.sale-price');


                    _addPriceType(jOriginalPrice, model);
                    _addPriceType(jSalePrice, model);


                    _jBox.attr('data-id', model.product_id);
                    _jBox.find('.label').html(model.label);
                    _jBox.find('.description').html(model.description);
                    _jBox.find('.ref').html(ref);


                    //----------------------------------------
                    // attributes
                    //----------------------------------------
                    // set the active state on the clicked attribute value
                    _jAttrSelector.empty();
                    var attributes = model["attributes"];
                    for (var name in attributes) {
                        var attr = attributes[name];
                        var sAttr = '' +
                            '<div class="attribute-selector cell-items">' +
                            '<div class="title">' + attr['label'] + '</div>';

                        if ('date' === name || attr.values.length > <?php echo $attrSwitchThreshold; ?>) {
                            sAttr += '<select class="s-simple-select">';
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
                        _jAttrSelector.append(sAttr);
                        _jAttrSelector.find('.s-simple-select').simpleselect();
                    }


                    var jImgCarousel = _jBox.find('.vertical-carousel');

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


                    prepareRefreshTriggers(_jBox);

                    var api = ekomApi.inst();
                    api.trigger('gui.onProductBoxUpdatedAfter', model);

                }


                if (false === ('ekomRefreshProductBox' in window)) {
                    window.ekomRefreshProductBox = function (uri, _jBox) {
                        $.post(uri, function (model) {
                            updateBoxByProductBoxModel(null, model, _jBox);
                        }, 'json');
                    };
                }


                if (false === ('ekomUpdateProductBoxQuantity' in window)) {
                    window.ekomUpdateProductBoxQuantity = function (quantity, _jBox) {
                        var jAvail = _jBox.find('.availability-container');
                        var jQtyInput = $('.quantity-input', _jBox);
                        _jBox.find('.virtual-quantity-number').html(quantity);


                        if (0 !== quantity) {
                            jAvail.addClass('availability-1');
                            jAvail.removeClass('availability-0');

                            _jBox.find('.add-to-cart-btn').removeClass('disabled');
                            _jBox.find('.add-to-estimate').removeClass('disabled');
                            _jBox.find('.purchase-express-btn').removeClass('disabled');

                            jQtyInput.val(1);
                        }
                        else {
                            jAvail.addClass('availability-0');
                            jAvail.removeClass('availability-1');

                            _jBox.find('.add-to-cart-btn').addClass('disabled');
                            _jBox.find('.add-to-estimate').addClass('disabled');
                            _jBox.find('.purchase-express-btn').addClass('disabled');

                            jQtyInput.val(0);
                        }
                    };
                }


                $(document).ready(function () {

                    var api = ekomApi.inst();


                    var isFormation = <?php echo (true === $isFormation) ? 'true' : 'false'; ?>;
                    var jTrainingProductDateRangeSelector;


                    var jBox = $('#<?php echo $boxCssId; ?>');
//                    var jAttrSelector = jBox.find(".attribute-selectors");


                    var jZoom1 = $('.zoom01', jBox);
                    var jVideoWrapper = $(".video-wrapper", jBox);

//                    api.on('cart.updated', function (cartModel) {
//                        var items = cartModel.items;
//                        for (var i in items) {
//                            var item = items[i];
//                            _cartQties[item.productIdentity] = item.quantity;
//                        }
//                        updateVirtualQuantity(cartModel);
//                    });
//                    api.on('cart.itemRemoved', function (productId) {
//                        _cartQties[productId] = 0;
//                        updateVirtualQuantity();
//                    });


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
                        else if (jTarget.hasClass("refresh-trigger")) {
                            jTarget = jTarget.closest('[data-ajax]');
                            processRefreshTrigger(jTarget, jBox);
                            return false;
                        }
                        else if (jTarget.hasClass("attribute-item")) {

                            jTarget = jTarget.closest('li');
                            processRefreshTrigger(jTarget, jBox);

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
                        else if (
                            jTarget.hasClass("add-to-cart-btn") ||
                            jTarget.hasClass("add-to-estimate") ||
                            jTarget.hasClass("purchase-express-btn")
                        ) {

                            var productId = jBox.attr("data-id");
                            var qty = jBox.find(".quantity-input").val();


                            /**
                             * @todo-ling: complementaryId, transform to details
                             * @type {number}
                             */
                            var complementaryId = 0;
                            if (isFormation) {
                                complementaryId = jTrainingProductDateRangeSelector.val();
                            }


                            /**
                             * Details in the cart should identify the product, same as attributes do.
                             * Structure of details should be (if set):
                             * - details:
                             *      - major: []
                             *      - minor: []
                             */
                            var details = {};
                            api.trigger('productBox.collectDetails', details);

//                            extraArgs.complementaryId = complementaryId; // disabled for now, maybe later in another form
                            if (
                                jTarget.hasClass("add-to-cart-btn") ||
                                jTarget.hasClass("add-to-estimate")
                            ) {
                                var options = {};
                                api.trigger('productBox.collectAddToCartOptions', options, jTarget);

                                if (jTarget.hasClass("add-to-cart-btn")) {
                                    api.cart.addItem(productId, qty, details, {}, options);
                                }
                                else {
                                    api.ekomEstimateJsApi.cart.addItemToEstimate(productId, qty, details, {
                                        complementaryId: complementaryId
                                    }, options);
                                }
                            }
                            else {
                                api.cart.addItem(productId, qty, details, {}, {
                                    onSuccess: function (model) {
                                        window.location.href = "<?php echo $uriCheckoutPage; ?>";
                                    }
                                });
                            }
                            return false;
                        }
//                        else if (jTarget.hasClass("add-to-estimate")) {
//
//                            var productId = jBox.attr("data-id");
//                            var qty = jBox.find(".quantity-input").val();
//
//                            var complementaryId = 0;
//                            if (isFormation) {
//                                complementaryId = jTrainingProductDateRangeSelector.val();
//                            }
//
//                            api.ekomEstimateJsApi.cart.addItemToEstimate(productId, qty, {
//                                complementaryId: complementaryId
//                            });
//                            return false;
//                        }
                        else if (jTarget.hasClass("add-to-bookmarks")) {

                            var productId = jBox.attr("data-id");
                            var productDetails = {};
                            api.trigger("productBox.collectDetails", productDetails);
                            api.user.addProductToWishlist(productId, productDetails);
                            return false;
                        }
                        else if (jTarget.hasClass("navigate-feature-link")) {
                            var hash = jTarget.attr("href");
                            api.trigger("scrollToFeatureBarItem", hash);
                            return false;
                        }
                    });


                    jBox.find('.s-simple-select').simpleselect();
                    prepareRefreshTriggers(jBox);


                    api.trigger('productBox.ready');


                });
                <?php if(true === $this->useDomContentLoaded): ?>
            });
            <?php endif; ?>
        </script>

        <?php
    }


    //--------------------------------------------
    //
    //--------------------------------------------
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
                    <input type="number" class="quantity-input" value="<?php echo $qty; ?>">
                </div>


            </div>
            <div class="add-to-bookmarks">
                <a class="bookmarks add-to-bookmarks" href="#">Ajouter à ma liste</a>
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