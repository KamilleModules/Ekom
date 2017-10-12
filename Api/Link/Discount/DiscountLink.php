<?php


namespace Module\Ekom\Api\Link\Discount;


use Module\Ekom\Utils\E;
use SaveOrmObject\Object\Ek\CurrencyObject;
use SaveOrmObject\Object\Ek\DiscountLangObject;
use SaveOrmObject\Object\Ek\DiscountObject;
use SaveOrmObject\Object\Ek\ProductCardHasDiscountObject;
use SaveOrmObject\Object\Ek\UserGroupObject;

class DiscountLink
{

    /**
     * @param array $info
     *  - ?shop_id:
     *  - ?lang_id:
     *
     *  - label: label of the discount
     *
     *  - procedure_type: amount|percent
     *  - procedure_operand: float|int, see more in the database.md document
     *
     *  - ?user_group: name of the iso group
     *  - ?currency: iso code 4217 (3 letters code uppercase, like EUR, USD, ...)
     *  - ?date_start (datetime)
     *  - ?date_end (datetime)
     *  - ?target: obsolete? @todo-ling
     *
     * @return array
     *
     */
    public function saveDiscountForProductCard($productCardId, array $info)
    {
        $r1 = [];
        $r2 = [];
        $discountObject = $this->getDiscountObjectByDiscountInfo($info);
        $d = $discountObject->save($r1);
        ProductCardHasDiscountObject::createByProductCardIdDiscountId($productCardId, $d['id'])
            ->setActive(1)
            ->save($r2);

        return array_replace($r1, $r2);
    }



    //--------------------------------------------
    //
    //--------------------------------------------

    private function getDiscountObjectByDiscountInfo(array $info)
    {

        $shopId = $this->getShopIdByInfo($info);
        $langId = $this->getLangIdByInfo($info);
        $discountInfo = $this->getDiscountInfo($info, $shopId);

        return DiscountObject::createUpdate('prm')
            ->feedByArray($discountInfo)
            ->createDiscountLang(DiscountLangObject::createUpdate()
                ->setLangId($langId)
                ->setLabel($info['label'])
            );
    }

    private function getDiscountInfo($info, $shopId)
    {
        $discountInfo = [
            'user_group_id' => null,
            'currency_id' => null,
            'date_start' => null,
            'date_end' => null,
            'procedure_type' => '',
            'procedure_operand' => '',
            'target' => '',
            'shop_id' => $shopId,
        ];

        $_discountInfo = $info;
        $userGroupId = null;
        if (array_key_exists('user_group', $_discountInfo) && null !== $_discountInfo['user_group']) {
            $userGroup = $_discountInfo['user_group'];
            $userGroupId = UserGroupObject::createByName($userGroup)->getId();
        }

        $currencyId = null;
        if (array_key_exists('currency', $_discountInfo) && null !== $_discountInfo['currency']) {
            $currency = $_discountInfo['currency'];
            $currencyId = CurrencyObject::createByIsoCode($currency)->getId();
        }


        unset($_discountInfo['user_group']);
        unset($_discountInfo['currency']);
        $discountInfo['user_group_id'] = $userGroupId;
        $discountInfo['currency_id'] = $currencyId;

        return array_replace($discountInfo, $_discountInfo);
    }

    private function getShopIdByInfo($info)
    {
        if (array_key_exists('shop_id', $info)) {
            $shopId = E::getShopId($info['shop_id']);
        } else {
            $shopId = E::getShopId();
        }
        return $shopId;
    }

    private function getLangIdByInfo($info)
    {
        if (array_key_exists('lang_id', $info)) {
            $langId = E::getShopId($info['lang_id']);
        } else {
            $langId = E::getShopId();
        }
        return $langId;
    }
}

