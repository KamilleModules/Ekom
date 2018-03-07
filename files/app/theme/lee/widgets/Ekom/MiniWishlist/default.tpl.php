<?php

use Bat\SessionTool;
use Module\Ekom\Utils\E;


?>
<li class="mini-wishlist-icon" id="mini-wishlist-icon">
    <?php

    $sClass = "lee-hidden";
    if ($v['quantity'] > 0) {
        $sClass = "";
    }
    ?>
    <div class="numberCircle <?php echo $sClass; ?>">
        <a href="<?php echo $v['uriMyWishList']; ?>">
            <span class="total-quantity">{quantity}</span>
        </a>
    </div>
    <a href="<?php echo $v['uriMyWishList']; ?>" class="lee-icon action action-bookmark">
        Favoris
    </a>
</li>

<script>


    jqueryComponent.ready(function () {
        var jMiniWishlist = $('#mini-wishlist-icon');

        var api = ekomApi.inst();
        api.on('user.wishlist.updated', function (model) {
            var nbItems = model.nbItems;
            if (nbItems > 0) {
                jMiniWishlist.find(".numberCircle").removeClass('lee-hidden');
            }
            else {
                jMiniWishlist.find(".numberCircle").addClass('lee-hidden');
            }
            jMiniWishlist.find(".total-quantity").html(nbItems);
        });
    });
</script>





