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
$subCats = [
    'equipement' => $catLayer->getSubCategoriesByName("equipement", 1),
    'formation' => $catLayer->getSubCategoriesByName("formation", 1),
    'events' => $catLayer->getSubCategoriesByName("events", 1),
    'conseil_communication' => $catLayer->getSubCategoriesByName("conseil_communication", 1),
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


?>
<div class="site">

    <div class="topbar" id="site-topbar">


        <div class="window">

            <div class="leftbar">
                <div class="lang-widget">
                    <img src="<?php echo $prefix; ?>/img/flags/fra.png" alt="french" class="lang-select-trigger">
                    <button class="lang-select-trigger">Choose your lang</button>
                    <div class="lang-widget-list">
                        <ul>
                            <li><a href="#"><img src="<?php echo $prefix; ?>/img/flags/fra.png" alt="french"><span>FRANCE</span></a></li>
                            <li><a href="#"><img src="<?php echo $prefix; ?>/img/flags/usa.png" alt="usa"><span>USA</span></a></li>
                        </ul>
                    </div>
                </div>
                <div class="hamburger-menu" id="topmenu-hamburger-container">
                    <a href="#" class="lee-icon hamburger-icon" id="topmenu-hamburger-icon-trigger">Hamburger menu</a>
                </div>


                <a href="#" class="menu-link" id="topmenu-catalogue-link">CATALOGUE</a>
                <div class="social-icons" id="topmenu-social-icons-container">
                    <ul id="topmenu-social-icons-ul" class="topmenu-social-icons-ul">
                        <li><a target="_blank" href="https://www.facebook.com/groupeleaderfit/" class="lee-icon social-icon social-icon-facebook">Facebook</a></li>
                        <li><a target="_blank" href="https://twitter.com/groupeleaderfit" class="lee-icon social-icon social-icon-twitter">Twitter</a></li>
                        <li><a target="_blank" href="https://www.instagram.com/Groupe_Leaderfit/" class="lee-icon social-icon social-icon-instagram">Instagram</a></li>
                        <li><a target="_blank" href="https://www.pinterest.fr/leaderfit/" class="lee-icon social-icon social-icon-pinterest">Pinterest</a></li>
                    </ul>
                </div>
                <a href="#" class="menu-link" id="topmenu-link">MY LEADERFIT</a>
            </div>

            <div class="searchbar-container">
                <div class="searchbar">
                    <div class="search-icon lee-icon" id="topmenu-searchtrigger-small"></div>
                    <form method="get"
                          action="<?php echo E::link("Ekom_searchResults"); ?>">
                        <input type="text" placeholder="Chercher" id="searchbar-input" name="search">
                    </form>
                    <button class="cancel-search lee-icon" id="searchbar-cancel">Cancel</button>
                    <ul>
                        <li class="topmenu-action-search"><span class="lee-icon action action-search">Recherche</span>
                        </li>
                    </ul>
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
                        <?php $l->widget("topActionBar.miniWishlist"); ?>
                        <?php $l->widget("topActionBar.miniEstimate"); ?>
                        <?php $l->widget("topActionBar.miniCart"); ?>
                    </ul>
                </div>
                <?php if (SessionUser::isConnected()): ?>
                    <div class="my-account-link">
                        <a href="<?php echo E::link("Ekom_customerDashboard"); ?>">My
                            account</a>
                        <div class="my-account-link-panel">
                            <ul>
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
        <ul class="topmenu-links">
            <?php foreach ($topCats as $cat): ?>
                <li><a href="<?php echo $cat['uri']; ?>"
                       class="panel-trigger"
                       data-id="panel-<?php echo $cat['name']; ?>"><?php echo $cat['label']; ?></a></li>
            <?php endforeach; ?>
        </ul>

        <div class="panels" id="topmenu-panels">
            <?php foreach ($subCats as $name => $cats): ?>
                <div class="panel panel-<?php echo $name; ?>" id="topmenu-panel-<?php echo $name; ?>">
                    <div class="menu-lists">
                        <?php foreach ($cats as $cat): ?>
                            <div class="list-with-heading">
                                <h4>
                                    <a href="<?php echo $cat['uri']; ?>"><?php
                                        $label = $cat['label'];
                                        echo substr($label, 0, -3); ?>
                                        <span class="ending"><?php echo substr($label, -3); ?></span>
                                    </a>
                                </h4>
                                <ul>
                                    <?php foreach ($cat['children'] as $subcat): ?>
                                        <li>
                                            <a href="<?php echo $subcat['uri']; ?>"><?php echo $subcat['label'];
                                                $count = $catLayer->countProductCards($subcat['category_id']);
                                                ?>
                                                <?php if ($count > 0): ?>
                                                (<?php echo $count; ?>
                                                )</a>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
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


    <!-- site-maincontent -->
    <div class="site-maincontent">
