<?php


namespace Theme\Lee\Ekom\ProductBox;

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\EkomProductCardVideo\View\EkomProductCardVideoViewHelper;
use Theme\LeeTheme;


class AjaxProductBoxRenderer extends ProductBoxRenderer
{


    public function __construct()
    {
        parent::__construct();
        $this->useDomContentLoaded = false;
        $this->showDescriptionLink = false;
        $this->useZoom = false;
        $this->useExpressPurchaseButton = true;
        $this->addToCartBtnExtraClass = 'ajaxian';
        $this->addToEstimateBtnExtraClass = 'ajaxian';
    }
}