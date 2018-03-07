<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Theme\Lee\Ekom\MiniCart\SidePanelRenderer;


KamilleThemeHelper::css("ekom-card-combination/sliding-mini-cart.css");
KamilleThemeHelper::css("mini-cart-sliding.css");
KamilleThemeHelper::loadJsInitFile($v);
//HtmlPageHelper::addBodyEndSnippet();


?>
<li class="mini-cart mini-cart-icon" id="mini-cart">
    <?php echo SidePanelRenderer::renderMiniCartInner($v); ?>
</li>


<div class="cd-panel from-right" id="mini-cart-content">
    <?php echo SidePanelRenderer::renderTop($v); ?>

    <div class="cd-panel-container">
        <div class="cd-panel-content">
            <?php echo SidePanelRenderer::render($v); ?>
        </div>
    </div>
</div>
<div class="bionic-marker"
     data-type="intent"
     data-value="minicart-sidebar"
></div>


<script>


    jqueryComponent.ready(function () {

        var jPanel = $('.cd-panel');
        var jMiniCart = $('#mini-cart');
        var api = ekomApi.inst();

        function openPanel() {
            jPanel.addClass('is-visible');
        }

        function closePanel() {
            jPanel.removeClass('is-visible');
        }


        //close the lateral panel
        jPanel.on('click', function (event) {


            var jTarget = $(event.target);


            if (false && "deprecated" && jTarget.hasClass("transform-to-estimate-trigger")) {
                api.ekomEstimateJsApi.cart.importEkomCart(function () {

                    var data = {};
                    api.trigger('ekom.onTransformToEstimateAfter', data);
                    if ('reload' in data) {
                        var reload = data.reload;
                        if (true === reload) {
                            window.location.reload();
                        }
                        else {
                            window.location.href = reload;
                        }
                    }
                    closePanel();
                });
                return false;
            }

            if ($(event.target).is('.cd-panel') || $(event.target).is('.cd-panel-close')) {
                closePanel();
                event.preventDefault();
            }
        });


        api.on('cart.updated', function (data) {
            var cartModel = data.cartModel;

            jPanel.find('.cd-panel-header').replaceWith(data.miniCartSidebarTop);
            jPanel.find('.mini-cart-content').replaceWith(data.miniCartSidebar);
            jMiniCart.empty().append(data.miniCartInner);
            if (0 === cartModel.items.length) {
                closePanel();
            }
        });
        api.on('cart.itemAdded', function (cartInfo) {
            var options = {
                willOpen: true
            };
            api.trigger('ekom.slidingCart.cartItemAddedOptions', options);
            if (true === options.willOpen) {
                openPanel();
            }
        });


        jMiniCart.on('click', function (e) {
            var jTarget = $(e.target);
            if (jTarget.hasClass('panel-trigger')) {
                openPanel();
                return false;
            }
        });


        $(document).keyup(function (e) {
            if (e.keyCode === 27) {
                closePanel();
            }
        });

    });

</script>





