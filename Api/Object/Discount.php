<?php


namespace Module\Ekom\Api\Object;


use Bat\StringTool;
use Module\Ekom\Api\GeneratedObject\GeneratedDiscount;
use Module\Ekom\Api\Layer\CategoryCoreLayer;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\ThisApp\Ekom\Helper\CategoryHelper;
use QuickPdo\QuickPdo;


class Discount extends GeneratedDiscount
{


    /**
     * Discount constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->addListener(['createAfter', "updateAfter"], function ($eventName, $table, $data, $third) {
            $discount_id = self::getIdFromCreateUpdate(func_get_args());


            //--------------------------------------------
            // FOR EVERY INTERACTION WITH THE DISCOUNT TABLE,
            // we first remove all bindings in ek_product_has_discount table,
            // then recreate them
            //--------------------------------------------
            QuickPdo::transaction(function () use ($discount_id, $data) {

                QuickPdo::delete("ek_product_has_discount", [
                    "id" => $discount_id,
                ]);

                $productIds = StringTool::unserializeAsArray($data['apply_product_ids']);
                $cardIds = StringTool::unserializeAsArray($data['apply_card_ids']);
                $categoryIds = StringTool::unserializeAsArray($data['apply_category_ids']);

                // we need to convert all to product ids
                $allProductIds = [];
                $allProductIds += $productIds;

                $category_card_ids = [];

                foreach ($categoryIds as $categoryId) {
                    $category_card_ids = array_merge($category_card_ids, CategoryLayer::getCardIdsByCategoryId($categoryId));
                }
                $cardIds = array_map("intval", $cardIds);
                $cardIds = array_merge($cardIds, $category_card_ids);
                $cardIds = array_unique($cardIds);

                if ($cardIds) {
                    $sCardIds = implode(',', $cardIds);
                    $product_ids_from_cards = QuickPdo::fetchAll("select id from ek_product where product_card_id in ($sCardIds)", [], \PDO::FETCH_COLUMN);
                    $allProductIds = array_merge($allProductIds, $product_ids_from_cards);
                }

                $allProductIds = array_unique($allProductIds);

                foreach ($allProductIds as $productId) {
                    QuickPdo::insert("ek_product_has_discount", [
                        'discount_id' => $discount_id,
                        'product_id' => $productId,
                    ]);
                }


            });
        });
    }


}