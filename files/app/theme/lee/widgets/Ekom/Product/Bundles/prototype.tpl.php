<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

$prefix = "/theme/" . ApplicationParameters::get("theme");
KamilleThemeHelper::css("product-bundles.css");

?>
<div class="window2 pt20 widget widget-product-bundles" id="widget-product-bundles">

    <div class="line top-title">
        <span class="main">PACKS</span>
    </div>
    <div style="clear: both"></div>


    <div class="bundles">
        <div class="bundle">
            <div class="product-images">
                <img src="/modules/Ekom/img/no-image.jpg" alt="product 1">
                <span class="plus">+</span>
                <img src="/modules/Ekom/img/no-image.jpg" alt="product 2">
            </div>
            <div class="payment-block">
                <div class="price-info">
                    <span class="price-with-tax line-through">25 € <abbr>TTC</abbr></span>
                    <span class="price-without-tax sale-price">20 € <abbr>TTC</abbr></span>
                </div>
                <div class="description">
                    <span>Ce pack comprend:</span>
                    <ul>
                        <li><input id="id1" type="checkbox" checked> <label for="id1">3 ballons XXX</label></li>
                        <li><input id="id2" type="checkbox" checked> <label for="id2">2 anneaux YYY</label></li>
                    </ul>
                </div>
                <button class="buy-button front-button button-red">ACHETER</button>
            </div>
        </div>
        <div class="bundle">
            <div class="product-images">
                <img src="/modules/Ekom/img/no-image.jpg" alt="product 1">
                <span class="plus">+</span>
                <img src="/modules/Ekom/img/no-image.jpg" alt="product 2">
                <span class="plus">+</span>
                <img src="/modules/Ekom/img/no-image.jpg" alt="product 3">
            </div>
            <div class="payment-block">
                <div class="price-info">
                    <span class="price-with-tax line-through">25 € <abbr>TTC</abbr></span>
                    <span class="price-without-tax sale-price">20 € <abbr>TTC</abbr></span>
                </div>
                <div class="description">
                    <span>Ce pack comprend:</span>
                    <ul>
                        <li><input id="id1" type="checkbox" checked> <label for="id1">3 ballons XXX</label></li>
                        <li><input id="id2" type="checkbox" checked> <label for="id2">2 anneaux YYY</label></li>
                        <li><input id="id3" type="checkbox" checked> <label for="id3">1 cerceau ZZZ</label></li>
                    </ul>
                </div>
                <button class="buy-button front-button button-red">ACHETER</button>
            </div>
        </div>
    </div>


</div>

