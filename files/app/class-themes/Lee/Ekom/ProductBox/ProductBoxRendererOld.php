<?php


namespace Theme\Lee\Ekom\ProductBox;

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\EkomProductCardVideo\View\EkomProductCardVideoViewHelper;
use Theme\LeeTheme;


class ProductBoxRendererOld
{

    protected $textDescriptionLink;
    protected $styleDescriptionLink;
    protected $positionRenderStock;
    protected $cssWidgetClass;


    public function __construct()
    {
        $this->textDescriptionLink = "Fiche technique détaillée";
        $this->styleDescriptionLink = "";
        $this->positionRenderStock = 1;
        $this->cssWidgetClass = "";
    }


    public static function create()
    {
        return new static();
    }

    public function render(array $model)
    {

        $v = $model;


        // product-box.md
        KamilleThemeHelper::css("product-box.css");
        LeeTheme::useLib("elevateZoom");
        LeeTheme::useLib("slick");


        $defaultSmallImageUri = "/modules/Ekom/img/no-image.jpg";
        $defaultLargeImageUri = $defaultSmallImageUri;


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
        <div class="widget widget-product-box product-box window <?php echo $this->cssWidgetClass; ?>"
             data-id="{product_id}"
             data-identity="{productIdentity}"
        >
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
                    <img id="zoom01"
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
                <div class="description-link" <?php echo $this->styleDescriptionLink; ?>>
                    <a href="#widget-product-features"
                       class="navigate-feature-link"><?php echo $this->textDescriptionLink; ?></a>
                </div>
                <?php


                $priceType = "TTC";
                if (true === $v['isB2B']) {
                    $priceType = "HT";
                }


                if (1 === $this->positionRenderStock) {
                    $this->renderStock($v);
                }

                ?>


                <div>
                    <?php if (true === $v['hasDiscount']): ?>
                        <div class="price">
                            <span class="price-value original-price has-discount"><?php echo $v['price']; ?></span>
                            <em><?php echo $priceType; ?></em>
                        </div>
                    <?php endif; ?>
                    <div class="price">
                        <span class="price-value sale-price"><?php echo $v['salePrice']; ?></span>
                        <em><?php echo $priceType; ?></em>
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

                $classDisabled = (false === $v['is_in_stock']) ? 'disabled' : '';

                ?>

                <div class="line-actions">
                    <button class="lee-black-button add-to-estimate <?php echo $classDisabled; ?>">Ajouter au devis
                    </button>
                    <?php
                    $sInactive = "";
                    if ($isFormation) {
                        $sInactive = 'disabled';
                    }
                    ?>
                    <button class="lee-red-button add-to-cart-btn <?php echo $sInactive; ?> <?php echo $classDisabled; ?>">
                        Ajouter au panier
                    </button>
                </div>

                <?php $this->renderBelowPaymentButtons($v); ?>


            </div>

        </div>


        <script>


            var images = <?php echo json_encode($v['images']); ?>;

            document.addEventListener("DOMContentLoaded", function (event) {

                $(document).ready(function () {


                    var api = ekomApi.inst();


                    /**
                     * Handling of quantities, see my comments somewhere in this class
                     **/
                    var _productIdentity = '<?php echo $v['productIdentity']; ?>';
                    var _productQty = <?php echo $v['quantity']; ?>;
                    var _virtualQty = 1;
                    var useDefaultVirtualQtySystem = true;

                    <?php
                    $cartQties = [
                        $v['productIdentity'] => $v['cartQuantity'],
                    ];
                    ?>
                    var _cartQties = <?php echo json_encode($cartQties); ?>;


                    window.ekomRefreshProductBox = function (uri) {
                        $.post(uri, function (model) {
                            updateBoxByProductBoxModel(null, model);
                        }, 'json');
                    };


                    function getVirtualQuantity(data) {

                        if (
                            'undefined' !== typeof data &&
                            'virtualQuantity' in data
                        ) {
                            return data.virtualQuantity;
                        }

                        if (true === useDefaultVirtualQtySystem) {

                            var v = _productQty;

                            if (_productIdentity in _cartQties) {
                                v -= _cartQties[_productIdentity];
                            }

                            if (v < 0) {
                                v = 0;
                            }
                            return v;
                        }
                        else {
                            return _virtualQty;
                        }
                    }


                    var alertQtyThreshold = <?php echo $alertQtyThreshold; ?>;
                    var isFormation = <?php echo (true === $isFormation) ? 'true' : 'false'; ?>;
                    var jTrainingProductDateRangeSelector;
                    var trainings;

                    var jBox = $('.product-box');
                    var jQtyInput = $('.quantity-input', jBox);

                    var jAttrSelector = jBox.find(".attribute-selectors");
                    var attrSwitchThreshold = <?php echo $attrSwitchThreshold; ?>;


                    api.on('cart.updated', function (cartModel) {
                        var items = cartModel.items;
                        for (var i in items) {
                            var item = items[i];
                            _cartQties[item.productIdentity] = item.quantity;
                        }
                        updateVirtualQuantity(cartModel);
                    });
                    api.on('cart.itemRemoved', function (productId) {
                        _cartQties[productId] = 0;
                        updateVirtualQuantity();
                    });


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

                    function nowPlayThisVideo(fileName) {
                        stopVideo();
                        jVideoWrapper.show();
                        var jVideo = jVideoWrapper.find('video[data-id="' + fileName + '"]');
                        jVideoWrapper.find('video').removeClass("active");
                        jVideo.addClass('active');
                        video = jVideo[0];
                        video.play();
                    }


                    /**
                     * This mechanism is thought for modules.
                     * If a module wants to invoke the "productBox page ajax refresher",
                     * they can simply add the refresh-trigger class to an element,
                     * and the data-ajax attribute with the desired uri as the value.
                     *
                     **/
                    function processRefreshTrigger(jTarget) {
                        var ajaxUri = jTarget.attr("data-ajax");
                        $.post(ajaxUri, function (model) {
                            updateBoxByProductBoxModel(jTarget, model);
                        }, 'json');
                    }


                    function prepareAttributesSelectors() {
                        jAttrSelector.find('select').each(function () {
                            $(this).off('change').on('change', function () {
                                var jOption = $(this).find(':selected');
                                processRefreshTrigger(jOption);
                            });
                        });
                    }

                    function updateBoxByProductBoxModel(jTarget, model) {

                        api.trigger('gui.onProductBoxUpdatedBefore', model);

                        // update global values
                        _productIdentity = model.productIdentity;


                        _productQty = model.quantity;
                        _cartQties[_productIdentity] = model.cartQuantity;
                        updateQuantity(model);

                        var ref = model.ref;
                        if (true) { // debug
                            ref += ' (#' + model.product_id + '-' + model.card_id + ')';
                        }


                        jBox.attr('data-id', model.product_id);
                        jBox.attr('data-identity', model.productIdentity);
                        jBox.find('.original-price').html(model.price);
                        jBox.find('.sale-price').html(model.salePrice);
                        jBox.find('.label').html(model.label);
                        jBox.find('.description').html(model.description);
                        jBox.find('.ref').html(ref);


                        //----------------------------------------
                        // attributes
                        //----------------------------------------
                        // set the active state on the clicked attribute value
                        jAttrSelector.empty();
                        var attributes = model["attributes"];
                        for (var name in attributes) {
                            var attr = attributes[name];
                            var sAttr = '' +
                                '<div class="attribute-selector cell-items">' +
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


                        /**
                         *
                         * 2017-09-14: I commented this statement because there is the same at the
                         * beginning of this function.
                         * Maybe it's an error?
                         **/
//                        updateQuantity(); //
                        prepareAttributesSelectors();


                        api.trigger('gui.onProductBoxUpdatedAfter', model);

                    }


                    /**
                     * If you start using this function, this code relies only on the
                     * _virtualQty variable to know the virtual quantity value.
                     *
                     **/
                    window.ekomProductBoxUpdateVirtualQuantity = function (virtualQty) {
                        if (null !== virtualQty) {

                            var data = {
                                virtualQuantity: virtualQty
                            };

                            useDefaultVirtualQtySystem = false;
                            updateVirtualQuantity(data);
                        }
                        else {
                            useDefaultVirtualQtySystem = true;
                        }
                    };

                    window.ekomProductBoxGetVirtualQuantity = function () {
                        return parseInt(jBox.find('.virtual-quantity-number').html());
                    };

                    //----------------------------------------
                    // QUANTITY HANDLER
                    //----------------------------------------
                    /**
                     * Reacts to virtual quantity changes.
                     * You should use it when the user updates her cart quantities.
                     *
                     * It does the following:
                     *      - disable/enable the purchase buttons if necessary
                     *      - updates the .virtual-quantity-number elements
                     *      - does not switch from available to not-available state (this is another method's job)
                     *
                     *
                     * - data: array|undefined
                     *      if array contains the following:
                     *              - ?virtualQuantity: the virtualQuantity number
                     *
                     **/
                    function updateVirtualQuantity(data) {
                        _virtualQty = getVirtualQuantity(data);
                        if (0 === _virtualQty) {
                            jBox.find('.add-to-cart-btn').addClass('disabled');
                            jBox.find('.add-to-estimate').addClass('disabled');
                        }
                        else {
                            jBox.find('.add-to-cart-btn').removeClass('disabled');
                            jBox.find('.add-to-estimate').removeClass('disabled');
                        }

                        jBox.find('.virtual-quantity-number').html(_virtualQty);

                        /**
                         * Not tested, but the idea is we don't want
                         * the virtualBox number to be higher than it could be after a cart update.
                         **/
                        fixQuantity();
                    }

                    /**
                     * This is the function you should use when switching from an attribute
                     * to another.
                     *
                     * It does the following:
                     * - check the new product quantity, and if necessary switches from the gui "available block"
                     *          to the "not-available block"
                     * - then calls the updateVirtualQuantity method to yield its visual results
                     *
                     *
                     **/
                    function updateQuantity(boxModel) {
                        var jAvail = jBox.find('.availability-container');
                        if (0 !== _productQty) {
                            jAvail.addClass('availability-1');
                            jAvail.removeClass('availability-0');
                            jQtyInput.val(1);
                        }
                        else {
                            jAvail.addClass('availability-0');
                            jAvail.removeClass('availability-1');
                            jQtyInput.val(0);
                        }
                        updateVirtualQuantity(boxModel);
                    }


                    function fixQuantity() {
                        var val = jQtyInput.val();
                        if (val < 0) {
                            val = 0;
                        }
                        else if (val > _virtualQty) {
                            val = _virtualQty;
                        }
                        jQtyInput.val(val);
                    }


                    jBox.find(".quantity-input").on('change keyup', function () {
                        fixQuantity();
                    });


                    //----------------------------------------
                    // CLICK HANDLER
                    //----------------------------------------
                    jBox.on('click', function (e) {


                        var jTarget = $(e.target);

                        if (jTarget.hasClass("disabled")) {
                            return false;
                        }
                        else if (jTarget.hasClass("refresh-trigger")) {
                            jTarget = jTarget.closest('[data-ajax]');
                            processRefreshTrigger(jTarget);
                            return false;
                        }
                        else if (jTarget.hasClass("attribute-item")) {

                            jTarget = jTarget.closest('li');
                            processRefreshTrigger(jTarget);

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
                                    jZoom1.attr('data-zoom-image', largeUri);
                                    jZoom1.data('zoom-image', largeUri).elevateZoom();

                                }
                                return false;
                            }
                        }
                        else if (jTarget.hasClass("add-to-cart-btn")) {

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
                             */
                            var details = {};
                            api.trigger('productBox.collectDetails', details);
                            console.log("pou");
                            console.log(details);

//                            extraArgs.complementaryId = complementaryId; // disabled for now, maybe later in another form
//                            api.cart.addItem(productId, qty, details);
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
                        else if (jTarget.hasClass("navigate-feature-link")) {
                            var hash = jTarget.attr("href");
                            api.trigger("scrollToFeatureBarItem", hash);
                            return false;
                        }
                    });

                    prepareAttributesSelectors();


                    api.trigger('productBox.ready');

                });
            });
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


        $quantity = $v['quantity'];
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
        <div class="availability-container availability-<?php echo (int)$isAvailable; ?>">
            <div class="availability availability-in-stock">
                En stock (<span
                        class="virtual-quantity-number"><?php echo $quantity; ?></span>
                exemplaires disponibles)
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

        $qty = (true === $model['is_in_stock']) ? '1' : '0';

        ?>
        <div class="line f-start-end">
            <div class="quantity">
                <div class="title">Quantité</div>
                <input class="quantity-input" type="number" value="<?php echo $qty; ?>">
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
}