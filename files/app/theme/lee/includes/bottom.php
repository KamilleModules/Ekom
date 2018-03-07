</div>
<!-- /site-maincontent -->

<div class="footer-separator">
    <div class="block left-block">
        <div class="inner">
            <div>
                <form action="post" method="post" class="bionic-context">
                    <span class="title">NEWSLETTER</span>
                    <input
                            class="bionic-target"
                            data-id="email"
                            type="text" placeholder="Votre email" value="">
                    <button class="bionic-btn"
                            data-action="user.subscribeToNewsletter"
                            data-param-email="$email"
                            type="submit">OK</button>
                </form>
            </div>
            <div class="promo-container">
                <span class="promo">Rejoignez-nous et recevez -5% sur votre première commande</span>
            </div>
        </div>
    </div>
    <div class="block right-block">
        <div class="title-container"
             style="position: relative; top: 3px;left: 13px;"
        ><span class="title">Professionnel ?</span></div>
        <div style="position: relative; top: 1px; left: 12px;">
            <a href="#">Identifiez-vous</a>
            et bénéficiez de tous vos avantages
        </div>
    </div>
</div>
<div class="footer">
    <div class="window top-footer">
        <div class="column-left">
            <h5>NOUS CONTACTER</h5>
            <span class="tel">+33 (0)2 47 52 66 01</span>
            <p class="opening-hours">
                du lundi au vendredi<br>
                de 9h00 à 12h30 - 14h00 à 17h30
            </p>
            <div class="address">
                Groupe Leaderfit<br>
                9 rue du Général Mocquery<br>
                37550 SAINT AVERTIN<br>
                FRANCE
            </div>
        </div>
        <div class="column-right">
            <div class="column-one">
                <h5>AIDE</h5>
                <ul>
                    <li><a href="#">Nous contacter</a></li>
                    <li><a href="#">F.A.Q</a></li>
                    <li><a href="#">SAV</a></li>
                    <li><a href="#">Retour produit</a></li>
                    <li><a href="#">Guide d'achat</a></li>
                    <li><a href="#">Paiement</a></li>
                    <li><a href="#">Prix professionnel</a></li>
                    <li><a href="#">Livraison</a></li>
                    <li><a href="#">Conditions générales de vente</a></li>
                    <li><a href="#">Plan du site</a></li>
                </ul>
            </div>
            <div class="column-two">
                <h5>GROUPE LEADERFIT'</h5>
                <ul>
                    <li><a href="#">Qui sommes-nous?</a></li>
                    <li><a href="#">Notre équipe</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="bottom-text">
    <p class="window">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
        dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex
        ea commodo consequat.Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat
        nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit
        anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
        laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae
        dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur 
    </p>
</div>
<div class="bottom-links">
    <ul>
        <li><a href="#">Mentions légales</a></li>
        <li><a href="#">Cookies</a></li>
        <li><a class="last" href="#">Politique de confidentialité</a></li>
    </ul>
</div>
</div>


<a href="#" class="affix" id="scroll-affix">Go up</a>
<div class="contact-affix" id="scroll-contact-affix">
    <div class="container-container">
        <div class="container">
            <div class="icon-phone"></div>
            <div class="t-center">
                <b>Contactez-nous au</b>
                <span class="phone-number">02 47 52 66 01</span>
            </div>
            <div class="icon-message"></div>
            <div class="t-center send-message">
                Envoyez-nous un message
            </div>
        </div>
    </div>
</div>


<div id="nin-shadow">
    <div id="floatBarsG">
        <div id="floatBarsG_1" class="floatBarsG"></div>
        <div id="floatBarsG_2" class="floatBarsG"></div>
        <div id="floatBarsG_3" class="floatBarsG"></div>
        <div id="floatBarsG_4" class="floatBarsG"></div>
        <div id="floatBarsG_5" class="floatBarsG"></div>
        <div id="floatBarsG_6" class="floatBarsG"></div>
        <div id="floatBarsG_7" class="floatBarsG"></div>
        <div id="floatBarsG_8" class="floatBarsG"></div>
    </div>
</div>
<div id="ajax-box-preview-target"></div>

<?php

use Core\Services\A;
use Kamille\Ling\Z;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;


$f = Z::themeDir() . "/includes/init.js";

HtmlPageHelper::addBodyEndSnippet('<script>' . PHP_EOL . file_get_contents($f) . PHP_EOL . '</script>');



