<?php


namespace Module\Ekom;


class EkomHooks
{


    //--------------------------------------------
    // CORE
    //--------------------------------------------
    protected static function Core_feedOnTheFlyFormProvider(\OnTheFlyForm\Provider\OnTheFlyFormProviderInterface $provider)
    {
        if ($provider instanceof \OnTheFlyForm\Provider\OnTheFlyFormProvider) {
            $provider->setNamespace("Ekom", 'Module\Ekom\OnTheFlyForm');
        }
    }

    protected static function Core_configureLawsUtil(\Kamille\Utils\Laws\LawsUtil $util)
    {
        $util->addShortCodeProvider(\Module\Ekom\ShortCodeProvider\EkomShortCodeProvider::create());
    }


    //--------------------------------------------
    // EKOM
    //--------------------------------------------
    protected static function Ekom_adaptContextualConfig(array &$conf)
    {

    }

    protected static function Ekom_feedCarrierCollection(\Module\Ekom\Carrier\Collection\CarrierCollection $collection)
    {

    }

    protected static function Ekom_feedPaymentMethodHandlerCollection(\Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollection $collection)
    {

    }

    protected static function Ekom_feedEkomProductPriceChain(\Module\Ekom\Price\PriceChain\EkomProductPriceChain $chain)
    {

    }

    protected static function Ekom_feedEkomCartPriceChain(\Module\Ekom\Price\PriceChain\EkomCartPriceChain $chain)
    {

    }

    protected static function Ekom_feedEkomTotalPriceChain(\Module\Ekom\Price\PriceChain\EkomTotalPriceChain $chain)
    {

    }

    //--------------------------------------------
    // NULLOS ADMIN
    //--------------------------------------------
    protected static function NullosAdmin_layout_sideBarMenuModel(array &$sideBarMenuModel)
    {
        $sideBarMenuModel['sections'][] = [
            "label" => "Ekom",
            "items" => [
                [
                    "icon" => "fa fa-home",
                    "label" => "test",
                    'badge' => [
                        'type' => "success",
                        'text' => "success",
                    ],
                    "items" => [
                        [
                            "icon" => "fa fa-but",
                            "label" => "bug",
                            "link" => "/pou",
                            "items" => null,
                        ],
                    ],
                ],
            ],
        ];
    }


    protected static function NullosAdmin_layout_addTopBarRightWidgets(array &$topbarRightWidgets)
    {

        $prefixUri = "/theme/" . \Kamille\Architecture\ApplicationParameters\ApplicationParameters::get("theme");
        $imgPrefix = $prefixUri . "/production";

        unset($topbarRightWidgets['topbar_right.userMessages']);

        $topbarRightWidgets["topbar_right.shopListDropDown"] = [
            "tpl" => "Ekom/ShopListDropDown/prototype",
            "conf" => [
                'nbMessages' => 10,
                'badgeColor' => 'red',
                'showAllMessagesLink' => true,
                'allMessagesText' => "See All Alerts",
                'allMessagesLink' => "/user-alerts",
                "messages" => [
                    [
                        "link" => "/ji",
                        "title" => "John Smith",
                        "image" => $imgPrefix . '/images/ling.jpg',
                        "aux" => "3 mins ago",
                        "message" => "Film festivals used to be do-or-die moments for movie makers. They were where...",
                    ],
                    [
                        "link" => "/ji",
                        "title" => "John Smith",
                        "image" => $imgPrefix . '/images/img.jpg',
                        "aux" => "12 mins ago",
                        "message" => "Film festivals used to be do-or-die moments for movie makers. They were where...",
                    ],
                ],
            ],
        ];
    }

}


