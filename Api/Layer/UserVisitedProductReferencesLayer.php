<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\Object\UserVisitedProductReference;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\SqlQueryWrapper\EkomSqlQueryWrapper;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class UserVisitedProductReferencesLayer
{


    /**
     * @param array $productBoxModel
     * @see EkomModels::productBoxModel()
     */
    public static function addVisitedReferenceByProductBoxModel(array $productBoxModel)
    {
        $userId = E::getUserId(null);
        if ($userId) {

            $productReferenceId = $productBoxModel['product_reference_id'];
            UserVisitedProductReference::getInst()->create([
                "user_id" => $userId,
                "product_reference_id" => $productReferenceId,
                "date" => date("Y-m-d H:i:s"),
            ]);
        }
    }


    public static function getLastVisitedProductsByProductBox(array $productBoxModel): array
    {
        $ret = [];
        $userId = E::getUserId(null);
        if ($userId) {
            $limit = 10;
            $ret = MiniProductBoxLayer::getLastVisitedBoxes($userId, $limit, [
                $productBoxModel['product_reference_id'],
            ]);
        }
        return $ret;

    }


}