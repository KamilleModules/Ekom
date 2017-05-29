<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomLinkHelper;
use QuickPdo\QuickPdo;


/**
 *
 */
class CartLayer
{

    public function addItem($qty, $productId)
    {


        $this->initSessionCart();

        $shopId = ApplicationRegistry::get("ekom.shop_id");

        if (true === SessionUser::isConnected()) {
            throw new \Exception("not implemented yet");
        } else {
            $_SESSION['ekom.cart'][$shopId][] = [
                "quantity" => $qty,
                "id" => $productId,
            ];
        }
    }


    public function getCartInfo()
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $model = [];
        $modelItems = [];
        $totalQty = 0;
        $totalWithoutTax = 0;
        $totalWithTax = 0;
        $displayTotal = 0;

        if (true === SessionUser::isConnected()) {
            throw new \Exception("not implemented yet");
        } else {

            $items = $_SESSION['ekom.cart'][$shopId];
            foreach ($items as $item) {

                $qty = $item['quantity'];
                $id = $item['id'];
                $totalQty += $qty;
                if (false !== ($it = $this->getCartItemInfo($id, $qty))) {
                    $modelItems[] = $it;
                    $totalWithoutTax += $qty  * $it['priceWithoutTaxUnformatted'];
                    $totalWithTax += $qty  * $it['priceWithTaxUnformatted'];
                    $displayTotal += $qty  * $it['displayPriceUnformatted'];

                }


            }
        }

        $model['totalQuantity'] = $totalQty;
        $model['items'] = $modelItems;
        $model['totalWithoutTax'] = E::price($totalWithoutTax);
        $model['totalWithTax'] = E::price($totalWithTax);
        $model['displayTotal'] = E::price($displayTotal);

        return $model;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function initSessionCart()
    {
        EkomApi::inst()->initWebContext();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        if (false === array_key_exists("ekom.cart", $_SESSION)) {
            $_SESSION['ekom.cart'] = [];
        }
        if (false === array_key_exists($shopId, $_SESSION['ekom.cart'])) {
            $_SESSION['ekom.cart'][$shopId] = [];
        }
    }


    private function getCartItemInfo($pId, $qty)
    {
        $shopId = (int)ApplicationRegistry::get("ekom.shop_id");
        $langId = (int)ApplicationRegistry::get("ekom.lang_id");
        $b = EkomApi::inst()->productLayer()->getProductBoxModelByProductId($pId, $shopId, $langId);
        if (array_key_exists('errorCode', $b)) {
            XLog::error("[Ekom module] - CartLayer.getCartItemInfo: product not found or request failed with product id: $pId");
            return false;
        } else {


//        - product_id: int, the product id
//    - product_slug: string, the product slug


            $row = QuickPdo::fetch("
select 
p.slug as product_slug,
c.slug as card_slug,            
l.slug as card_default_slug,
l.product_card_id,
b.reference
            
from ek_shop_has_product_lang p            
inner join ek_product b on b.id=p.product_id
inner join ek_product_card_lang l on l.product_card_id=b.product_card_id and l.lang_id=p.lang_id
inner join ek_shop_has_product_card_lang c on c.shop_id=p.shop_id and c.product_card_id=b.product_card_id and c.lang_id=p.lang_id            
            
where 
p.shop_id=$shopId            
and p.lang_id=$langId            
            ");


            $productCardId = $row['product_card_id'];
            $productSlug = $row['reference'];
            $cardSlug = ('' !== $row['card_slug']) ? $row['card_slug'] : $row['card_default_slug'];


            
            $attr = $b['attributes'];
            $zeAttr = [];
            foreach ($attr as $name => $info) {
                $values = $info['values'];
                foreach ($values as $val) {
                    if ('1' === $val['selected']) {
                        $zeAttr[] = [
                            "attribute_id" => $info['attribute_id'],
                            "label" => $info['label'],
                            "value" => $val['value'],
                        ];
                        break;
                    }
                }
            }


            $mainImage = "";
            if (count($b['images']) > 0) {
                $arr = current($b['images']);
                $mainImage = $arr['thumb']; // small?
            }


            return [
                'product_id' => $b['product_id'],
                'label' => $b['label'],
                'ref' => $b['ref'],
                'uri' => E::link("Ekom_product", ['slug' => $productSlug]),
                'remove_uri' => EkomLinkHelper::getUri("removeProductFromCart", $pId),
                'update_qty_uri' => EkomLinkHelper::getUri("updateCartProduct", $pId),
                'uri_card' => E::link("Ekom_productCard", ['slug' => $cardSlug]),
                'product_card_id' => $productCardId,
                'attributes' => $zeAttr,
                'discount_price' => $b['discount_price'],
                'displayPrice' => $b['displayPrice'],
                'displayPriceUnformatted' => $b['displayPriceUnformatted'],
                'priceWithoutTax' => $b['priceWithoutTax'],
                'priceWithoutTaxUnformatted' => $b['priceWithoutTaxUnformatted'],
                'priceWithTax' => $b['priceWithTax'],
                'priceWithTaxUnformatted' => $b['priceWithTaxUnformatted'],
                'image' => $mainImage,
            ];

        }

    }

}
