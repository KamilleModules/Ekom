<?php

use Bat\SessionTool;
use Module\Ekom\Utils\E;
use Theme\Lee\ThisApp\Ecp\SuccessNotifRenderer;
use Theme\LeeTheme;


LeeTheme::useLib('featherlight');


?>
<li class="mini-estimate-icon go-to-estimate-trigger" id="mini-estimate-icon">
    <?php

    $sClass = "lee-hidden";
    if ($v['quantity'] > 0) {
        $sClass = "";
    }
    ?>
    <div class="numberCircle <?php echo $sClass; ?> go-to-estimate-trigger"><span
                class="total-quantity go-to-estimate-trigger">{quantity}</span></div>

    <span class="lee-icon action action-estimate go-to-estimate-trigger">Devis</span>
</li>

<div style="display:none">
    <?php echo SuccessNotifRenderer::render([
        'id' => "estimate-item-added-modal",
        'msg' => "Ce produit a bien été ajouté à votre devis.",
        'link' => [
            "text" => "Voir mon devis",
            "href" => $v['uriEstimateCart'],
        ],
    ]); ?>
</div>


<script>


    jqueryComponent.ready(function () {


        var jMiniEstimate = $('#mini-estimate-icon');
        var jModal = $("#estimate-item-added-modal");

        var api = ekomApi.inst();
        api.on('estimateCart.itemAdded', function (data) {
//                alert("Le produit a bien été ajouté au devis");
            jMiniEstimate.find(".numberCircle").removeClass('lee-hidden');
            jMiniEstimate.find(".total-quantity").html(data.estimateCartModel.cartTotalQuantity);
            $.featherlight(jModal);
        });

        api.on('estimateCart.updated', function (data) {
            var q = data.estimateCartModel.cartTotalQuantity;
            if (q > 0) {
                jMiniEstimate.find(".numberCircle").removeClass('lee-hidden');
            }
            else {
                jMiniEstimate.find(".numberCircle").addClass('lee-hidden');
            }
            jMiniEstimate.find(".total-quantity").html(data.estimateCartModel.cartTotalQuantity);
        });

        jMiniEstimate.on('click', function (e) {
            var jTarget = $(e.target);
            if (jTarget.hasClass("go-to-estimate-trigger")) {
                location.href = "<?php echo $v['uriEstimateCart']; ?>";
                return false;
            }
        });

    });
</script>





