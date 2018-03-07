<?php


namespace Theme\Lee\Ekom\Carousel;


use ArrayToString\ArrayToStringTool;
use Kamille\Services\XLog;

class CarouselItemRenderer
{


    public static function create()
    {
        return new static();
    }


    public function render(array $p)
    {
        if (false === array_key_exists('errorCode', $p)) {

            $htTTC = 'HT';
            if (true === $p['taxHasTax']) {
                $htTTC = 'TTC';
            }
            $hasNouveaute = $p['hasNovelty'];
            ?>
            <div class="product-box ekt-pc" data-pid="<?php echo $p['product_id']; ?>"
                 data-href="<?php echo $p['uriCard']; ?>">
                <div class="slickinner ekt-pc">
                    <?php if (true === $p['discountHasDiscount']): ?>
                        <span class="promo">PROMO</span>
                    <?php endif; ?>
                    <?php if (true === $hasNouveaute): ?>
                        <span class="newproduct">NOUVEAUTÉ</span>
                    <?php endif; ?>
                    <div class="inner ekt-pc">
                        <div class="image-container ekt-pc">
                            <img
                                    class="ekt-pc"
                                    src="<?php echo htmlspecialchars($p['imageSmall']); ?>"
                                    alt="<?php echo htmlspecialchars($p['label']); ?>">
                        </div>
                        <a href="<?php
                        echo htmlspecialchars($p['uriCard']); ?>"
                           class="title ekt-pc"><?php echo $p['label']; ?></a>
                        <span class="ref ekt-pc">Réf: <?php echo $p['ref']; ?></span>

                        <div class="bottom ekt-pc">
                            <?php if (true === ($p['discountHasDiscount'])): ?>
                                <div class="oldprice ekt-pc">
                                    <span class="amount ekt-pc"><?php echo $p['priceBase']; ?></span>
                                    <span class="exponent ekt-pc"><?php echo $htTTC; ?></span>
                                </div>
                            <?php endif; ?>
                            <span class="price ekt-pc"><?php echo $p['priceSale']; ?><span
                                        class="exponent ekt-pc"><?php echo $htTTC; ?></span></span>
                        </div>
                    </div>
                    <button class="product-preview-trigger bionic-btn"
                            data-action="user.addProductToWishlist"
                            data-param-product_id="<?php echo $p['product_id']; ?>"
                    >Preview
                    </button>
                </div>
            </div>
            <?php
        } else {
            XLog::error("errorCode found in template: class-themes/Lee/Ekom/Carousel/CarouselItemRenderer.php, with errors: " . ArrayToStringTool::toPhpArray($p));
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function renderOld(array $p)
    {

        $htTTC = 'TTC';
        if (true === $p['isB2B']) {
            $htTTC = 'HT';
        }
        $hasNouveaute = false;
        ?>
        <div class="product-box">
            <div class="slickinner">
                <?php if (true === $p['hasDiscount']): ?>
                    <span class="promo">PROMO</span>
                <?php endif; ?>
                <?php if (true === $hasNouveaute): ?>
                    <span class="newproduct">NOUVEAUTÉ</span>
                <?php endif; ?>
                <div class="inner">
                    <div class="image-container">
                        <img src="<?php echo htmlspecialchars($p['imageSmall']); ?>"
                             alt="<?php echo htmlspecialchars($p['label']); ?>">
                    </div>
                    <a class="link-card" href="<?php
                    echo htmlspecialchars($p['uriCard']); ?>"
                       class="title"><?php echo $p['label']; ?></a>
                    <div class="bottom">
                        <?php if (true === ($p['hasDiscount'])): ?>
                            <div class="oldprice">
                                <span class="amount"><?php echo $p['price']; ?></span>
                                <span class="exponent"><?php echo $htTTC; ?></span>
                            </div>
                        <?php endif; ?>
                        <span class="price"><?php echo $p['salePrice']; ?><span
                                    class="exponent"><?php echo $htTTC; ?></span></span>
                    </div>
                </div>
                <button class="product-preview-trigger">Preview</button>
            </div>
        </div>
        <?php
    }
}