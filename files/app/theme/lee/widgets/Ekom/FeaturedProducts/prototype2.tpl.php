<?php


use Core\Services\A;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Theme\LeeTheme;

$prefix = "/theme/" . ApplicationParameters::get("theme");


$products = array_merge([
    [
        'hasPromo' => true,
        'hasNouveaute' => true,
        'imgSrc' => "$prefix/img/products/chaussettes.jpg",
        'imgAlt' => "chaussettes",
        'title' => "Chaussettes Pilates",
        'oldPrice' => "10000.56",
        'price' => "10000.56",
    ],
    [
        'hasPromo' => false,
        'hasNouveaute' => false,
        'imgSrc' => "$prefix/img/products/kettle-bell.jpg",
        'imgAlt' => "kettle bell",
        'title' => "Kettlebell",
        'oldPrice' => "",
        'price' => "20",
    ],
    [
        'hasPromo' => false,
        'hasNouveaute' => false,
        'imgSrc' => "$prefix/img/products/lfpilates.jpg",
        'imgAlt' => "lfpilates",
        'title' => "Brique Yoga",
        'oldPrice' => "",
        'price' => "20",
    ],
    [
        'hasPromo' => false,
        'hasNouveaute' => false,
        'imgSrc' => "$prefix/img/products/barre.jpg",
        'imgAlt' => "barre",
        'title' => "Parallel bar",
        'oldPrice' => "",
        'price' => "20",
    ],
    [
        'hasPromo' => false,
        'hasNouveaute' => false,
        'imgSrc' => "$prefix/img/products/roue.jpg",
        'imgAlt' => "roue",
        'title' => "Roue Yoga",
        'oldPrice' => "",
        'price' => "20",
    ],
], []
//    ,EkomApi::inst()->getFeaturedProducts()
);

$initJs = $v['__DIR__'] . "/init.js";
A::addBodyEndJsCode("jquery", file_get_contents($initJs));

LeeTheme::useLib("slick");

?>


<div class="featured-products">
    <div class="window">
        <span class="title">{title}</span>
        <div class="product-boxes" id="featured-products-boxes">
            <?php foreach ($products as $p):
                $slug = (array_key_exists('slug', $p)) ? $p['slug'] : 'kettle-bell';
                ?>
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
                                <img src="<?php echo htmlspecialchars($p['imgSrc']); ?>"
                                     alt="<?php echo htmlspecialchars($p['imgAlt']); ?>">
                            </div>
                            <a href="<?php
                            echo htmlspecialchars(E::link("Ekom_productCard", ['productName' => $slug])); ?>"
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










