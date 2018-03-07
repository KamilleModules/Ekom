<?php


namespace Theme\Lee\Ekom\ProductBox;


class AjaxTrainingProductBoxRenderer extends TrainingProductBoxRenderer
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