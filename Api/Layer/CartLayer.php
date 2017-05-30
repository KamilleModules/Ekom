<?php


namespace Module\Ekom\Api\Layer;


use ArrayToString\ArrayToStringTool;
use Authenticate\SessionUser\SessionUser;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\CartLocalStore;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomLinkHelper;
use QuickPdo\QuickPdo;


/**
 *
 */
class CartLayer
{

    /**
     * @var CartLocalStore
     */
    private $cartLocalStore;

    public function addItem($qty, $productId)
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $productId = (int)$productId;
        $alreadyExists = false;
        foreach ($_SESSION['ekom.cart'][$shopId] as $k => $item) {
            if ((int)$item['id'] === $productId) {
                $_SESSION['ekom.cart'][$shopId][$k]['quantity'] += $qty;
                $alreadyExists = true;
                break;
            }
        }

        if (false === $alreadyExists) {
            $_SESSION['ekom.cart'][$shopId][] = [
                "quantity" => $qty,
                "id" => $productId,
            ];
        }
        $this->writeToLocalStore();
    }

    public function removeItem($productId)
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $productId = (int)$productId;
        foreach ($_SESSION['ekom.cart'][$shopId] as $k => $item) {
            if ((int)$item['id'] === $productId) {
                unset($_SESSION['ekom.cart'][$shopId][$k]);
                break;
            }
        }

        $this->writeToLocalStore();
    }

    public function updateItemQuantity($productId, $newQty)
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $productId = (int)$productId;
        $alreadyExists = false;
        foreach ($_SESSION['ekom.cart'][$shopId] as $k => $item) {
            if ((int)$item['id'] === $productId) {
                $_SESSION['ekom.cart'][$shopId][$k]['quantity'] = $newQty;
                $alreadyExists = true;
                break;
            }
        }

        if (false === $alreadyExists) {
            $_SESSION['ekom.cart'][$shopId][] = [
                "quantity" => $newQty,
                "id" => $productId,
            ];
        }
        $this->writeToLocalStore();
    }


    public function prepareUserCart()
    {
        if (null !== ($userId = SessionUser::getValue('id'))) {
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
            if (false === array_key_exists("ekom.cart", $_SESSION) || false === array_key_exists($shopId, $_SESSION['ekom.cart'])) {
                // the user doesn't have a session cart yet, we create it from the localStorage if any.

                $userCart = $this->getCartLocalStore()->getUserCart($userId, $shopId);
                $_SESSION['ekom.cart'][$shopId] = $userCart;

            } else {
                // the user already has a cart in session, she will use it, we don't need to do anything
            }
        } else {
            XLog::error("[Ekom module] - CartLayer.prepareUserCart: SessionUser not connected or doesn't have an id. sessionDump: " . ArrayToStringTool::toPhpArray($_SESSION));
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

        $items = $_SESSION['ekom.cart'][$shopId];
        foreach ($items as $item) {

            $qty = $item['quantity'];
            $id = $item['id'];
            $totalQty += $qty;
            if (false !== ($it = $this->getCartItemInfo($id))) {
                $it['quantity'] = $qty;
                $modelItems[] = $it;
                $totalWithoutTax += $qty * $it['priceWithoutTaxUnformatted'];
                $totalWithTax += $qty * $it['priceWithTaxUnformatted'];
                $displayTotal += $qty * $it['displayPriceUnformatted'];
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


    private function getCartItemInfo($pId)
    {
        $pId = (int)$pId;
        $shopId = (int)ApplicationRegistry::get("ekom.shop_id");
        $langId = (int)ApplicationRegistry::get("ekom.lang_id");

        return A::cache()->get("Module.Ekom.Api.Layer.CartLayer.getCartItemInfo.$shopId.$langId.$pId", function () use ($pId, $shopId, $langId) {


            $b = EkomApi::inst()->productLayer()->getProductBoxModelByProductId($pId, $shopId, $langId);
            if (array_key_exists('errorCode', $b)) {
                XLog::error("[Ekom module] - CartLayer.getCartItemInfo: product not found or request failed with product id: $pId");
                return $b;
            } else {


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
b.id=$pId
and p.shop_id=$shopId            
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

        }, [
            "ek_shop_has_product_card_lang.*",
            "ek_shop_has_product_card.*",
            "ek_product_card_lang.*",
            "ek_product_card.*",
            "ek_shop.*",
            "ek_product_has_product_attribute.*",
            "ek_product_attribute_lang.*",
            "ek_product_attribute_value_lang.*",
            "ek_product.*",
            "ekomApi.image.product",
            "ekomApi.image.productCard",
        ]);
    }

    private function writeToLocalStore()
    {
        if (true === SessionUser::isConnected()) {
            $shopId = ApplicationRegistry::get("ekom.shop_id");
            if (null !== ($userId = SessionUser::getValue('id'))) {
                $this->getCartLocalStore()->saveUserCart($userId, $shopId, $_SESSION['ekom.cart'][$shopId]);
            } else {
                XLog::error("[Ekom module] - CartLayer: in shop#$shopId, this user doesn't have an id: " . ArrayToStringTool::toPhpArray($_SESSION));
            }
        }
    }


    /**
     * @return CartLocalStore
     */
    private function getCartLocalStore()
    {
        if (null === $this->cartLocalStore) {
            $this->cartLocalStore = new CartLocalStore();
        }
        return $this->cartLocalStore;
    }
}
