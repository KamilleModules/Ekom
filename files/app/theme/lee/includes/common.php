<?php

use Core\Services\X;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\E;
use Module\NullosAdmin\ThemeHelper\ThemeHelper;
use Theme\LeeTheme;


$prefixUri = "/theme/" . ApplicationParameters::get("theme");

HtmlPageHelper::setLang("en");
HtmlPageHelper::addMetaBlock('
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- Meta, title, CSS, favicons, etc. -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
');


HtmlPageHelper::css("$prefixUri/css/style.css");
HtmlPageHelper::css("$prefixUri/css/hero-slider.css");
//HtmlPageHelper::css("$prefixUri/libs/slick/slick.css");


// bottom scripts
LeeTheme::useLib('jquery');
HtmlPageHelper::js("$prefixUri/libs/picturefill/picturefill.min.js", null, null, false);


E::loadEkomJsApi();


LeeTheme::useLib('bionic');


LeeTheme::useLib('cloneTemplate');
//HtmlPageHelper::js("$prefixUri/libs/slick/slick.js", null, null, false);

LeeTheme::useLib('slick');


if (array_key_exists('jsScripts', $v)) {
    foreach ($v['jsScripts'] as $uri) {
        HtmlPageHelper::js($uri, null, null, false);
    }
}


