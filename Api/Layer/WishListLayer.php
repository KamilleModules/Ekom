<?php


namespace Module\Ekom\Api\Layer;


use Bat\StringTool;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class WishListLayer
{

    public static function getActiveWishListProductReferenceIds(int $userId)
    {
        return QuickPdo::fetchAll("
select product_reference_id 
from ek_user_has_product_reference 
where user_id=$userId
and date_deleted is null 
", [], \PDO::FETCH_COLUMN);
    }

    public static function getWishListItemsByUserId($userId, $limit = null, $type = "current")
    {
        $userId = (int)$userId;
        $q = "
select 
product_reference_id,
date_added

from ek_user_has_product_reference 
where user_id=$userId
         
        ";
        if ('current' === $type) {
            $q .= "
and date_deleted is null            
            ";
        } elseif ("deleted" === $type) {
            $q .= "
and date_deleted is not null            
            ";
        }

        $q .= "
order by `date_added` desc        
        ";

        if (null !== $limit) {
            $limit = (int)$limit;
            $q .= "
limit 0, $limit
            ";
        }


        $referenceIds = [];
        $wishlistRows = QuickPdo::fetchAll($q, [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        foreach ($wishlistRows as $productReferenceId => $dateAdded) {
            $referenceIds[] = $productReferenceId;
        }
        $rows = MiniProductBoxLayer::getBoxesByProductReferenceIds($referenceIds);
        foreach ($rows as $k => $row) {
            $row['wishlist_date'] = $wishlistRows[$row['product_reference_id']];
            $rows[$k] = $row;
        }
        return $rows;

    }


    public static function removeUserWishlistItem($userId, $productId)
    {
        QuickPdo::update("ek_user_has_product", [
            "date_deleted" => date("Y-m-d H:i:s"),
        ], [
            ["user_id", "=", $userId],
            ["product_id", "=", $productId],
            " and date_deleted is null",
        ]);
    }

    public static function removeUserWishlist($userId)
    {
        QuickPdo::update("ek_user_has_product_reference", [
            "date_deleted" => date("Y-m-d H:i:s"),
        ], [
            ["user_id", "=", $userId],
            " and date_deleted is null",
        ]);
    }


    /**
     * Add a product to the user's wishlist,
     * and by default returns the number of wishlist items owned by the user.
     *
     * The number of user's wishlist items is returned only if the
     * $n argument is strictly equals to zero and the number has changed (the product that the user just
     * added was not already in her wishlist)
     *
     *
     * @param $pId
     * @param null $userId
     * @param int $n
     * @return bool|mixed
     */
    public function addToWishList(int $productReferenceId, int $userId = null, &$n = 0)
    {

        if (null === $userId) {
            $userId = E::getUserId();
        }


        if (false === ($row = QuickPdo::fetch("
        select user_id from ek_user_has_product_reference 
        where
         user_id=$userId 
         and product_reference_id=$productReferenceId
         and date_deleted is null
"))) {
            EkomApi::inst()->userHasProductReference()->create([
                'user_id' => $userId,
                'product_reference_id' => $productReferenceId,
                'date_added' => date("Y-m-d H:i:s"),
                'date_deleted' => null,
            ]);

            if (0 === $n) {
                $n = $this->getNbUserWishItems($userId);
            }
            return true;
        }
        return false;
    }


    public function getMiniWishlistModel()
    {
        return [
            'uriMyWishList' => E::link("Ekom_customerWishList"),
            'quantity' => $this->getNbUserWishItems(),
        ];
    }


//    public function getWishListItemsByUserId($userId, ListParamsInterface $params = null)
//    {
//
//        //--------------------------------------------
//        // PREPARING ROWS
//        //--------------------------------------------
//        $player = EkomApi::inst()->productLayer();
//        $userId = (int)$userId;
//        $rows = QuickPdo::fetchAll("select product_id, product_details from ek_user_has_product where user_id=$userId and date_deleted is null");
//        $pRows = [];
//        foreach ($rows as $row) {
//            $productDetails = $row['product_details'];
//            if ('' !== $productDetails) {
//                $productDetails = unserialize($productDetails);
//            }
//            if (!is_array($productDetails)) {
//                $productDetails = [];
//            }
//            $pRows[] = $player->getProductBoxModelByProductId($row['product_id'], null, null, $productDetails);
//        }
//
//        //--------------------------------------------
//        // CONFIGURING PARAMS
//        //--------------------------------------------
//        $params->setAllowedSortFields([
//            'product_id',
//            'label',
//            'rawSalePrice',
//        ]);
//
//
//        //--------------------------------------------
//        // APPLYING PARAMS
//        //--------------------------------------------
//        $pRows = ListParamsUtil::applyParams($params, $pRows);
//        return $pRows;
//    }


    public static function getNbUserWishItems($userId = null, $type = 'current')
    {
        if (null === $userId) {

            $userId = E::getUserId(null);
            if (null === $userId) {
                return 0;
            }
        }
        $q = "select count(*) as count from ek_user_has_product_reference 
where user_id=$userId";
        if ('current' === $type) {
            $q .= "
and date_deleted is null";
        } elseif ('deleted' === $type) {
            $q .= "
and date_deleted is not null";
        }
        return QuickPdo::fetch($q, [], \PDO::FETCH_COLUMN);
    }

    public static function getFirstFavoriteAddedDateByUserId($userId)
    {
        $userId = (int)$userId;
        return QuickPdo::fetch("
select date_added from ek_user_has_product_reference
where user_id=$userId
 order by date_added asc        
        ", [], \PDO::FETCH_COLUMN);
    }
}