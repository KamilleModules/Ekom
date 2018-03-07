<?php


use Theme\Lee\Ekom\ProductBox\GenericProductBoxRenderer;

?>
<div id="widget-productbox-leaderfit" class="bionic-context">
    <?php echo GenericProductBoxRenderer::render($v); ?>
</div>
<script>


    jqueryComponent.ready(function () {


        var api = ekomApi.inst();
        var jContainer = $('#widget-productbox-leaderfit');

        api.on("product.infoReady", function (data) {
            jContainer.empty().append(data.productBox);
        });
    });
</script>