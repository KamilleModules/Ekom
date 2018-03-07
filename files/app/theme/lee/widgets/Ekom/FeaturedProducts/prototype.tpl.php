<?php


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Module\Ekom\Utils\E;
use Theme\LeeTheme;


LeeTheme::useLib('slick');

$prefix = "/theme/" . ApplicationParameters::get("theme");

$products = [
    [
        'hasPromo' => true,
        'hasNouveaute' => true,
        'imgSrc' => "/img/products/chaussettes.jpg",
        'imgAlt' => "chaussettes",
        'title' => "Chaussettes Pilates",
        'oldPrice' => "10000.56",
        'price' => "10000.56",
    ],
    [
        'hasPromo' => false,
        'hasNouveaute' => false,
        'imgSrc' => "/img/products/kettle-bell.jpg",
        'imgAlt' => "kettle bell",
        'title' => "Kettlebell",
        'oldPrice' => "",
        'price' => "20",
    ],
    [
        'hasPromo' => false,
        'hasNouveaute' => false,
        'imgSrc' => "/img/products/lfpilates.jpg",
        'imgAlt' => "lfpilates",
        'title' => "Brique Yoga",
        'oldPrice' => "",
        'price' => "20",
    ],
    [
        'hasPromo' => false,
        'hasNouveaute' => false,
        'imgSrc' => "/img/products/barre.jpg",
        'imgAlt' => "barre",
        'title' => "Parallel bar",
        'oldPrice' => "",
        'price' => "20",
    ],
    [
        'hasPromo' => false,
        'hasNouveaute' => false,
        'imgSrc' => "/img/products/roue.jpg",
        'imgAlt' => "roue",
        'title' => "Roue Yoga",
        'oldPrice' => "",
        'price' => "20",
    ],

];


?>
<div class="featured-products">
    <div class="window">
        <span class="title">{title}</span>
        <div class="product-boxes" id="featured-products-boxes">
            <?php foreach ($products as $p): ?>
                <div class="product-box">
                    <div class="slickinner">
                        <?php if (true === $p['hasPromo']): ?>
                            <span class="promo">PROMO</span>
                        <?php endif; ?>
                        <?php if (true === $p['hasNouveaute']): ?>
                            <span class="newproduct">NOUVEAUTÉ</span>
                        <?php endif; ?>
                        <div class="inner">
                            <div class="image-container">
                                <img src="<?php echo $prefix . htmlspecialchars($p['imgSrc']); ?>"
                                     alt="<?php echo htmlspecialchars($p['imgAlt']); ?>">
                            </div>
                            <a href="<?php
                            echo htmlspecialchars(E::link("Ekom_productCard", ['productName' => "kettle-bell"])); ?>"
                               class="title"><?php echo $p['title']; ?></a>
                            <div class="bottom">
                                <?php if (!empty($p['oldPrice'])): ?>
                                    <div class="oldprice">
                                        <span class="amount"><?php echo $p['oldPrice']; ?>€</span>
                                        <span class="exponent">TTC</span>
                                    </div>
                                <?php endif; ?>
                                <span class="price"><?php echo $p['price']; ?>€ <span
                                            class="exponent">TTC</span></span>
                            </div>
                        </div>
                        <button class="product-preview-trigger">Preview</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

