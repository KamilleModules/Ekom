<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class WishListLayer
{


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
    public function addToWishList($pId, $userId = null, &$n = 0)
    {

        if (null === $userId) {
            EkomApi::inst()->initWebContext();
            $userId = E::getUserId();
        }
        $userId = (int)$userId;
        $pId = (int)$pId;
        $ret = false;

        if (false === ($row = QuickPdo::fetch("
        select user_id from ek_user_has_product 
        where user_id=$userId and product_id=$pId
"))) {
            EkomApi::inst()->userHasProduct()->create([
                'user_id' => $userId,
                'product_id' => $pId,
                'date' => date("Y-m-d H:i:s"),
                'order' => 0,
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
            'quantity' => $this->getNbUserWishItems(),
        ];
    }

    public function getNbUserWishItems($userId = null)
    {
        if (null === $userId) {
            EkomApi::inst()->initWebContext();
            $userId = E::getUserId(null);
            if (null === $userId) {
                return 0;
            }
        }

        return QuickPdo::fetch("select count(*) as count from ek_user_has_product where user_id=$userId", [], \PDO::FETCH_COLUMN);
    }
}