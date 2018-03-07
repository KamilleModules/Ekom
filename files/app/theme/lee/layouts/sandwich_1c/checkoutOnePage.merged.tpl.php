<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;

$uri = "/theme/" . ApplicationParameters::get("theme");

HtmlPageHelper::css($uri . "/css/payment.css");


?>
<div>
    <div class="topbar">
        <div class="inner window">
            <a href="/" class="logo left">
                <img src="<?php echo $uri . "/img/logo-small.png"; ?>" alt="Secured">
            </a>

            <span class="middle font-title">Passer la commande ( <a href="#">2 articles</a> )</span>

            <a href="#https://www.amazon.fr/gp/help/customer/display.html?ie=UTF8&nodeId=3329781&ref_=ox_spc_privacy"
               class="right">
                <img src="<?php echo $uri . "/img/icons/secured.png"; ?>" alt="Secured">
            </a>
        </div>
    </div>


    <div class="maincontent window">
        <?php $l->widget('checkout'); ?>
    </div>
</div>