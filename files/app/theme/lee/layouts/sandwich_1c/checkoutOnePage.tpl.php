<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Theme\LeeTheme;

LeeTheme::useLib('jquery');
LeeTheme::useLib('onTheFlyForm');
E::loadEkomJsApi();


$uri = "/theme/" . ApplicationParameters::get("theme");

HtmlPageHelper::css($uri . "/css/payment.css");


$cartModel = EkomApi::inst()->cartLayer()->getCartModel();


?>
<div>
    <div class="topbar">
        <div class="inner window">
            <a href="/" class="logo left">
                <img src="<?php echo $uri . "/img/logo-small.png"; ?>" alt="Secured">
            </a>

            <span class="middle font-title">Passer la commande ( <a
                        href="<?php echo E::link("Ekom_cart"); ?>"><?php echo $cartModel['totalQuantity']; ?>
                    articles</a> )</span>

            <a href="#https://www.amazon.fr/gp/help/customer/display.html?ie=UTF8&nodeId=3329781&ref_=ox_spc_privacy"
               class="right">
                <img src="<?php echo $uri . "/img/icons/secured.png"; ?>" alt="Secured">
            </a>
        </div>
    </div>


    <div class="window">
        <?php $l->widget('checkout'); ?>
    </div>
</div>