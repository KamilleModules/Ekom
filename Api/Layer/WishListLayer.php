<?php


namespace Module\Ekom\Api\Layer;


use ListParams\ListParamsInterface;
use ListParams\Util\ListParamsUtil;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class WishListLayer
{


    public static function removeUserWishlistItem($userId, $productId)
    {
        QuickPdo::update("ek_user_has_product", [
            "deleted_date" => date("Y-m-d H:i:s"),
        ],[
            ["user_id", "=", $userId],
            ["product_id", "=", $productId],
            " and deleted_date is null",
        ]);
    }

    public static function removeUserWishlist($userId)
    {
        QuickPdo::update("ek_user_has_product", [
            "deleted_date" => date("Y-m-d H:i:s"),
        ], [
            ["user_id", "=", $userId],
            " and deleted_date is null",
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
    public function addToWishList($pId, array $productDetails = [], $userId = null, &$n = 0)
    {

        if (null === $userId) {
            EkomApi::inst()->initWebContext();
            $userId = E::getUserId();
        }
        $userId = (int)$userId;
        $pId = (int)$pId;

        if ($productDetails) {
            $sProductDetails = serialize($productDetails);
        } else {
            $sProductDetails = "";
        }


        if (false === ($row = QuickPdo::fetch("
        select user_id from ek_user_has_product 
        where
         user_id=$userId 
         and product_id=$pId
         and product_details='$sProductDetails'
         and deleted_date is null
"))) {
            EkomApi::inst()->userHasProduct()->create([
                'user_id' => $userId,
                'product_id' => $pId,
                'product_details' => $sProductDetails,
                'date' => date("Y-m-d H:i:s"),
                'deleted_date' => null,
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


    public function getWishListItemsByUserId($userId, ListParamsInterface $params = null)
    {

        //--------------------------------------------
        // PREPARING ROWS
        //--------------------------------------------
        $player = EkomApi::inst()->productLayer();
        $userId = (int)$userId;
        $rows = QuickPdo::fetchAll("select product_id, product_details from ek_user_has_product where user_id=$userId and deleted_date is null");
        $pRows = [];
        foreach ($rows as $row) {
            $productDetails = $row['product_details'];
            if ('' !== $productDetails) {
                $productDetails = unserialize($productDetails);
            }
            if (!is_array($productDetails)) {
                $productDetails = [];
            }
            $pRows[] = $player->getProductBoxModelByProductId($row['product_id'], null, null, $productDetails);
        }

        //--------------------------------------------
        // CONFIGURING PARAMS
        //--------------------------------------------
        $params->setAllowedSortFields([
            'product_id',
            'label',
            'rawSalePrice',
        ]);


        //--------------------------------------------
        // APPLYING PARAMS
        //--------------------------------------------
        $pRows = ListParamsUtil::applyParams($params, $pRows);
        return $pRows;
    }


    public static function getNbUserWishItems($userId = null)
    {
        if (null === $userId) {
            EkomApi::inst()->initWebContext();
            $userId = E::getUserId(null);
            if (null === $userId) {
                return 0;
            }
        }
        return QuickPdo::fetch("select count(*) as count from ek_user_has_product 
where user_id=$userId
and deleted_date is null
", [], \PDO::FETCH_COLUMN);
    }
}