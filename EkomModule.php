<?php


namespace Module\Ekom;


use Core\Module\ApplicationModule;
use Core\Services\A;
use Kamille\Services\XConfig;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;
use XiaoApi\Helper\GeneralHelper\GeneralHelper;

class EkomModule extends ApplicationModule
{

    protected function getInstallDbScripts()
    {

        $dbName = XConfig::get("Core.database");

        return [
            "ekom.database_structure" => str_replace("mydb", $dbName, file_get_contents(__DIR__ . "/assets/db/ekom.sql")),
        ];
    }

    protected function getPlanets()
    {
        return [
            /**
             * @todo-ling: complete the list of dependencies...
             */
            'ling.FileDeletor',
        ];
    }


    protected function installDatabase()
    {

        A::quickPdoInit();
        $query = $this->getSqlQueryByFile("kamille", __DIR__ . "/assets/db/ekom.sql");
        $this->output->notice("Installing Ekom database structure...");
        QuickPdo::freeQuery($query);
    }

    protected function uninstallDatabase()
    {
        A::quickPdoInit();

        if (true === "todo") {


            /**
             * Todo: this has not been tested and will probably fail, because of the order in which the
             * tables are executed.
             * Rather than finding the right order,
             * I suggest that the implementor put all this in transaction and de-activate foreign key,
             * so that the order doesn't matter.
             * (if that's possible of course...)
             */
            $tables = [
                'ek_address',
                'ek_backoffice_user',
                'ek_carrier',
                'ek_cart_discount',
                'ek_cart_discount_lang',
                'ek_category',
                'ek_category_has_discount',
                'ek_category_has_product_card',
                'ek_category_lang',
                'ek_country',
                'ek_country_lang',
                'ek_coupon',
                'ek_coupon_has_cart_discount',
                'ek_coupon_lang',
                'ek_currency',
                'ek_discount',
                'ek_discount_lang',
                'ek_feature',
                'ek_feature_lang',
                'ek_feature_value',
                'ek_feature_value_lang',
                'ek_lang',
                'ek_order',
                'ek_order_has_order_status',
                'ek_order_status',
                'ek_order_status_lang',
                'ek_password_recovery_request',
                'ek_payment_method',
                'ek_product',
                'ek_product_attribute',
                'ek_product_attribute_lang',
                'ek_product_attribute_value',
                'ek_product_attribute_value_lang',
                'ek_product_bundle',
                'ek_product_bundle_has_product',
                'ek_product_card',
                'ek_product_card_has_discount',
                'ek_product_card_has_tax_group',
                'ek_product_card_lang',
                'ek_product_comment',
                'ek_product_has_discount',
                'ek_product_has_feature',
                'ek_product_has_product_attribute',
                'ek_product_lang',
                'ek_product_type',
                'ek_seller',
                'ek_shop',
                'ek_shop_configuration',
                'ek_shop_has_address',
                'ek_shop_has_carrier',
                'ek_shop_has_currency',
                'ek_shop_has_lang',
                'ek_shop_has_payment_method',
                'ek_shop_has_product',
                'ek_shop_has_product_card',
                'ek_shop_has_product_card_lang',
                'ek_shop_has_product_lang',
                'ek_tax',
                'ek_tax_group',
                'ek_tax_group_has_tax',
                'ek_tax_lang',
                'ek_timezone',
                'ek_user',
                'ek_user_group',
                'ek_user_has_address',
                'ek_user_has_product',
                'ek_user_has_user_group',
            ];
            foreach ($tables as $table) {
                $method = GeneralHelper::tableNameToClassName($table, 'ek_');
                $method = lcfirst($method);
                EkomApi::inst()->$method()->deleteAll();
                EkomApi::inst()->$method()->drop();
            }
        }
    }
}


