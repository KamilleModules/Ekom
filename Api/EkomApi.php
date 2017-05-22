<?php


namespace Module\Ekom\Api;

use Bat\SessionTool;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Services\XLog;
use Module\Ekom\Api\Layer\BreadCrumbsLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Api\Layer\ShopLayer;


/**
 * The ekom api.
 *
 */
class EkomApi extends GeneratedEkomApi
{
    //--------------------------------------------
    //
    //--------------------------------------------
    protected function log($type, $message) // override me
    {
        XLog::log($type, $message);
    }


    /**
     * Should be called by every ekom web controller that displays a page.
     * It creates the basic variables available through the (kamille) ApplicationRegistry:
     *
     * - ekom.shop_id
     * - ekom.lang_id
     * - ekom.currency_id
     *
     * It will also create the following variables in the $_SESSION:
     *
     * - ekom
     * ----- $host
     * --------- shop_id
     * --------- lang_id
     * --------- currency_id
     * --------- timezone: the name of the timezone
     *
     *
     * and use them for subsequent calls.
     * To clean these "cache" variables, use the cleanInitCache method.
     *
     * (might be useful if you make changes in the backoffice and want to see your changes in the front)
     *
     *
     * The host is identified using the shop table.
     * If the host cannot be recognized (for instance because there is no corresponding entry in the shop table),
     * then the lang is initialized to "eng", the currency to "USD", and the shop_id to 0.
     * That's the signal that something went wrong for other tools.
     * Also, a message is sent to the log.
     *
     *
     *
     */
    public function initWebPage()
    {

        SessionTool::start();

        $host = Z::request()->host();
az($shopRow = $this->shopLayer()->getShopInfoByHost($host));

        if (array_key_exists("ekom", $_SESSION) && array_key_exists($host, $_SESSION['ekom'])) {
            $fromSession = true;
            $sess = $_SESSION['ekom'];
            $shopId = $sess['id'];
            $langId = $sess['lang_id'];
            $currencyId = $sess['currency_id'];
        } else {
            $fromSession = false;
            $shopId = 0;
            $langId = "eng";
            $currencyId = "USD";
            if (false !== ($shopRow = $this->shopLayer()->getShopInfoByHost($host))
            ) {
                $shopId = $shopRow['id'];
                $langId = $shopRow['lang_id'];
                $currencyId = $shopRow['currency_id'];
            } else {
                XLog::error("[Ekom module] - EkomApi: No shop found with host $host");
            }
            $_SESSION['ekom'] = [
                $host => [
                    "shop_id" => $shopId,
                    "lang_id" => $langId,
                    "currency_id" => $currencyId,
                ],
            ];
        }
        ApplicationRegistry::set("ekom.shop_id", $shopId);
        ApplicationRegistry::set("ekom.lang_id", $langId);
        ApplicationRegistry::set("ekom.currency_id", $currencyId);
        ApplicationRegistry::set("ekom.fromSession", $fromSession); // used for personal debugging, don't rely on it

    }

    public function cleanInitCache()
    {
        SessionTool::destroyPartial("ekom");
    }

    //--------------------------------------------
    //
    //--------------------------------------------

    /**
     * @return BreadCrumbsLayer
     */
    public function breadCrumbsLayer()
    {
        return $this->getLayer('breadCrumbsLayer');
    }

    /**
     * @return ProductLayer
     */
    public function productLayer()
    {
        return $this->getLayer('productLayer');
    }

    /**
     * @return ShopLayer
     */
    public function shopLayer()
    {
        return $this->getLayer('shopLayer');
    }
}