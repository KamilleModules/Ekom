<script>

//----------------------------------------
// This is the cartSpreader script
// It spreads the cartModel to the js codespace
//----------------------------------------
jqueryComponent.ready(function () {
    var cartModel = <?php echo json_encode($v); ?>;
    ekomApi.inst().trigger('cartModel.ready', cartModel);
    window.cartModel = cartModel;
    ekomApi.inst().on('cart.updated', function (cartModel) {
        window.cartModel = cartModel;
    });
});


</script>