<?php


use Authenticate\SessionUser\SessionUser;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Ling\Z;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Services\XConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Module\EkomFastSearch\ProductSearch\FastProductSearch;
use QuickPdo\QuickPdo;
use Theme\LeeTheme;


$catLayer = EkomApi::inst()->categoryLayer();
$topCats = $catLayer->getSubCategoriesByName("home", 0, 'special');
foreach ($topCats as $k => $v) {
    if ('conseil_communication' === $v['slug']) {
        unset($topCats[$k]);
    }
}

$subCats = [
    'equipement' => $catLayer->getSubCategoriesByName("equipement", 1),
    'formation' => $catLayer->getSubCategoriesByName("formation", 1),
    'events' => $catLayer->getSubCategoriesByName("events", 1),
//    'conseil_communication' => $catLayer->getSubCategoriesByName("conseil_communication", 1),
];


$promoItems = [
    [
        "Aide au montage de votre dossier",
        "Pour toute inscription à une formation",
    ],
    [
        "-5% sur votre première commande",
        "En vous inscrivant à notre newsletter",
    ],
    [
        "Bénéficiez des prix professionnels",
        "Jusqu'à -50% sur le matériel. S'identifier",
    ],
    [
        "Chapeau pointu",
        "Turlututu razorback aimspace",
    ],
];


$prefix = "/theme/" . ApplicationParameters::get("theme");
//HtmlPageHelper::css($prefix . "/css/ultimo.css");
//LeeTheme::useLib("autocomplete");
LeeTheme::useLib("myautocomplete");
LeeTheme::useLib("cloneTemplate");


//a(date("Y-m-d H:i:s", 1496670896));
//a(SessionUser::isConnected());


//az($subCats);

?>
<script>
    //----------------------------------------
    // https://github.com/lingtalfi/jqueryComponent
    //----------------------------------------
    window.jqueryComponent = {
        ready: function (callback) {
            if (window.jQuery) {
                callback();
            }
            else {
                document.addEventListener("DOMContentLoaded", function (event) {
                    $(document).ready(function () {
                        callback();
                    });
                });
            }
        }
    };
</script>
<div class="site">

    <div class="topbar" id="site-topbar">


        <div class="window">

            <div class="leftbar">
                <?php if (array_key_exists("menu", $_GET)): ?>
                    <div class="lang-widget">
                        <img src="<?php echo $prefix; ?>/img/flags/fra.png" alt="french" class="lang-select-trigger">
                        <button class="lang-select-trigger">Choose your lang</button>
                        <div class="lang-widget-list">
                            <ul>
                                <li><a href="#"><img src="<?php echo $prefix; ?>/img/flags/fra.png" alt="french"><span>FRANCE</span></a>
                                </li>
                                <li><a href="#"><img src="<?php echo $prefix; ?>/img/flags/usa.png"
                                                     alt="usa"><span>USA</span></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="hamburger-menu" id="topmenu-hamburger-container">
                        <a href="#" class="lee-icon hamburger-icon" id="topmenu-hamburger-icon-trigger">Hamburger
                            menu</a>
                    </div>


                    <a href="#" class="menu-link" id="topmenu-catalogue-link">CATALOGUE</a>
                <?php endif; ?>
                <div class="social-icons" id="topmenu-social-icons-container">
                    <ul id="topmenu-social-icons-ul" class="topmenu-social-icons-ul">
                        <li><a target="_blank" href="https://www.facebook.com/groupeleaderfit/"
                               class="lee-icon social-icon social-icon-facebook">Facebook</a></li>
                        <li><a target="_blank" href="https://twitter.com/groupeleaderfit"
                               class="lee-icon social-icon social-icon-twitter">Twitter</a></li>
                        <li><a target="_blank" href="https://www.instagram.com/Groupe_Leaderfit/"
                               class="lee-icon social-icon social-icon-instagram">Instagram</a></li>
                        <li><a target="_blank" href="https://www.pinterest.fr/leaderfit/"
                               class="lee-icon social-icon social-icon-pinterest">Pinterest</a></li>
                    </ul>
                </div>
                <!--                <a href="#" class="menu-link" id="topmenu-link">MY LEADERFIT</a>-->
            </div>

            <div class="spacer"></div>
            <div class="searchbar-container">
                <div class="searchbar">
                    <form method="get"
                          autocomplete="off"
                          action="<?php echo E::link("Ekom_searchResults"); ?>">
                        <input class="topsearch-input" type="text" placeholder="Chercher" id="searchbar-input"
                               name="search">
                    </form>
                    <div class="search-icon lee-icon" id="topmenu-searchtrigger-small"></div>
                    <button class="cancel-search lee-icon" id="searchbar-cancel">Cancel</button>
                </div>
                <div class="search-results-panel">
                    <div class="section section-categories">
                        <div class="title">SUGGESTION DE CATÉGORIES</div>
                        <ul class="items-list-categories"></ul>
                    </div>
                    <div class="section section-products">
                        <div class="title">SUGGESTION DE PRODUITS</div>
                        <ul class="items-list">
                            <?php
                            /**
                             *
                             *
                             * $rows = FastProductSearch::create()->getResults('kettle');
                             * foreach ($rows as $row): ?>
                             * <li class="item">
                             * <div class="image">
                             * <img src="<?php echo $row['uri_thumb']; ?>"
                             * alt="kettle bell">
                             * </div>
                             * <span class="label"><?php echo $row['label']; ?></span>
                             * <span class="attributes"><?php echo $row['attr_string']; ?></span>
                             * <span class="ref"><?php echo $row['ref']; ?></span>
                             * <span class="price">
                             * <span class="price-text"><?php echo $row['sale_price']; ?></span>
                             * <abbr class="price-type">HT</abbr></span>
                             * </li>
                             * <?php endforeach; ?>
                             */
                            ?>
                        </ul>
                    </div>
                    <div class="templates">
                        <ul class="items-list">
                            <li class="item" data-uri="{-uri_card-}">
                                <div class="image">
                                    <img data-src="{-uri_thumb-}"
                                         alt="{-%label-}">
                                </div>
                                <span class="label">{-label-}</span>
                                <span class="attributes">{-attr_string-}</span>
                                <span class="ref">ref. {-ref-}</span>
                                <span class="price">
                                        <span class="price-text">{-sale_price-}</span>
                                        <abbr class="price-type">{-price_type-}</abbr></span>
                            </li>
                        </ul>
                        <ul class="items-list-categories">
                            <li class="item" data-uri="{-uriCategory-}"><a href="{-uriCategory-}">{-label-}</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="rightbar">
                <div class="action-icons">
                    <ul>
                        <li class="mini-search-icon" id="mini-search-icon">
                            <a href="#" class="lee-icon action action-search main-search-trigger">Recherche</a>
                        </li>
                        <?php $l->widget("topActionBar.miniWishlist"); ?>
                        <?php $l->widget("topActionBar.miniEstimate"); ?>
                        <?php $l->widget("topActionBar.miniCart"); ?>
                    </ul>
                </div>
                <?php if (SessionUser::isConnected()): ?>
                    <div class="my-account-link">
                        <a class="my-account-link-href" href="<?php echo E::link("Ekom_customerDashboard"); ?>"
                           class="connexion">MON COMPTE</a>
                        <div class="my-account-link-panel">
                            <ul>
                                <li><a href="?<?php echo XConfig::get("Authenticate.disconnectGetKey"); ?>=1">Se
                                        déconnecter</a></li>
                                <li><a href="?<?php echo XConfig::get("Authenticate.disconnectGetKey"); ?>=1">Se
                                        déconnecter</a></li>
                                <li><a href="?<?php echo XConfig::get("Authenticate.disconnectGetKey"); ?>=1">Se
                                        déconnecter</a></li>
                                <li><a href="?<?php echo XConfig::get("Authenticate.disconnectGetKey"); ?>=1">Se
                                        déconnecter</a></li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo E::link("Ekom_login"); ?>" class="connexion">SE CONNECTER/ S'INSCRIRE</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="header"><h1><a href="<?php echo E::link('Ekom_home'); ?>">Leaderfit</a></h1></div>
    <div class="topmenu">
        <ul class="topmenu-links window">
            <?php foreach ($topCats as $cat): ?>
                <li class="topmenu-link"><a href="<?php echo $cat['uri']; ?>"
                                            class="panel-trigger"
                                            data-id="panel-<?php echo $cat['name']; ?>"><?php echo $cat['label']; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>
    <div class="panels window topmenu-panels" id="topmenu-panels">
        <?php foreach ($subCats as $name => $cats):

            $name = htmlspecialchars($name);
            ?>
            <div class="panel panel-<?php echo $name; ?> topmenu-panel" id="topmenu-panel-<?php echo $name; ?>">
                <div class="menu-lists">
                    <?php
                    foreach ($cats as $cat):
                        ?>
                        <div class="list-with-heading cat-<?php echo $cat['name']; ?>">
                            <h4>
                                <?php

                                if (false === strpos($cat['label'], '/')): ?>
                                    <a href="<?php echo htmlspecialchars($cat['uri']); ?>"><?php
                                        $label = $cat['label'];
                                        $style = '';
                                        if (strlen($label) <= 3) {
                                            $style = ' style="margin-left:0px"';
                                        }
                                        echo substr($label, 0, -3); ?>
                                        <span class="ending" <?php echo $style; ?>><?php echo substr($label, -3); ?></span><?php
                                        ?>
                                    </a>
                                <?php else:
                                    $p = explode('/', $cat['label']);
                                    $firstLabel = trim($p[0]);
                                    $secondLabel = trim($p[1]);


                                    ?>
                                    <a href="<?php echo htmlspecialchars($cat['uri']); ?>"><?php
                                        echo substr($firstLabel, 0, -3); ?>
                                        <span class="ending"
                                              style="margin-right: 0px"><?php echo substr($firstLabel, -3); ?></span>
                                        /
                                        <?php echo substr($secondLabel, 0, -3); ?>
                                        <span class="ending"><?php echo substr($secondLabel, -3); ?></span>
                                    </a>
                                <?php endif; ?>
                            </h4>


                            <?php if ('management' !== $cat['name']): ?>


                                <ul>
                                    <?php

                                    foreach ($cat['children'] as $subcat): ?>
                                        <li>
                                            <a href="<?php echo htmlspecialchars($subcat['uri']); ?>"><?php echo ucfirst(mb_strtolower($subcat['label'])); ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="topmenu-tables">
                                    <?php

                                    $nbTables = 3;
                                    $nb = count($cat['children']);
                                    $size = floor($nb / $nbTables);
                                    $c = 0;


                                    for ($j = 1; $j <= $nbTables; $j++): ?>
                                        <table>
                                            <tr>
                                                <td>
                                                    <ul>
                                                        <?php for ($i = 1; $i <= $size; $i++):
                                                            $subcat = $cat['children'][$c++];
                                                            ?>
                                                            <li>
                                                                <a href="<?php echo htmlspecialchars($subcat['uri']); ?>"><?php echo ucfirst(mb_strtolower($subcat['label'])); ?></a>
                                                            </li>
                                                        <?php endfor; ?>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </table>
                                    <?php endfor; ?>
                                </div>

                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="topmenu-right-promo">
                    <a href="#">
                        <img src="/modules/ThisApp/topmenu/promo-equipement.jpg" alt="promotion équipement">
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="topmenu-panels-cancel-overlay"></div>
    <?php if (HtmlPageHelper::hasBodyClass("page-home")): ?>
        <div class="our-services" id="our-services">
            <div class="container" id="ourservices-slick">
                <?php foreach ($promoItems as $info):
                    list($title, $desc) = $info;
                    ?>
                    <div class="block">
                        <span class="title"><?php echo $title; ?></span>
                        <span class="description"><?php echo $desc; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>


    <!-- site-maincontent -->
    <div class="site-maincontent">
