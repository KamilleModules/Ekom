<?php


namespace Module\Ekom\Api\Layer;


use Bat\StringTool;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;

class ProductReferenceHasDiscountLayer
{

    public static function recreateBindingsByDiscountData(array $data){

        $discount_id = $data['id'];

        //--------------------------------------------
        // FOR EVERY INTERACTION WITH THE DISCOUNT TABLE,
        // we first remove all bindings in ek_product_has_discount table,
        // then recreate them
        //--------------------------------------------
        QuickPdo::transaction(function () use ($discount_id, $data) {


            QuickPdo::delete("ek_product_reference_has_discount", [
                ["discount_id", "=", $discount_id],
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


            if ($allProductIds) {


                $sProductIds = implode(', ', $allProductIds);


                // convert this to references
                $productReferenceIds = QuickPdo::fetchAll("select id from ek_product_reference where product_id in ($sProductIds)", [], \PDO::FETCH_COLUMN);



                foreach ($productReferenceIds as $productReferenceId) {
                    QuickPdo::insert("ek_product_reference_has_discount", [
                        'discount_id' => $discount_id,
                        'product_reference_id' => $productReferenceId,
                    ]);
                }
            }


        });
    }

}