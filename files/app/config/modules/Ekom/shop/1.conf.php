<?php


$conf = [
//    'stockShowQtyThreshold' => 10, // deprecated: waste of resources
    'acceptOutOfStockOrders' => false,
    'sessionTimeout' => 300000, // 50 minutes
//    'carrierSelectionMode' => "manual", // deprecated, now application dev decides for herself...
    'checkoutMode' => "singleAddress",
    'statusProvider' => "lee",
    'attribute2TemplateAdaptor' => "Module\Ekom\Laws\DynamicWidgetBinder\Attribute2TemplateAdaptor\Attribute2TemplateAdaptor",
    /**
     * Which country is to use in forms (search for countryLayer()->getCountryIdByIso)
     */
    'countryIso' => "FR",
];