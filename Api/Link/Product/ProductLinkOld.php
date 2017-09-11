<?php


namespace Module\Ekom\Api\Link\Product;


use SaveOrmObject\Object\Ek\CategoryObject;
use SaveOrmObject\Object\Ek\ProductAttributeObject;
use SaveOrmObject\Object\Ek\ProductAttributeValueObject;
use SaveOrmObject\Object\Ek\ProductCardObject;
use SaveOrmObject\Object\Ek\ProductObject;
use SaveOrmObject\Object\Ek\ShopHasProductCardObject;
use SaveOrmObject\Object\Ek\ShopHasProductObject;

class ProductLinkOld
{


    private $products;
    /**
     * @var ProductCardObject
     */
    private $card;
    private $shopHasProduct;


    public function __construct()
    {
        $this->card = null; // mandatory
        $this->products = []; // at least one
        $this->shopHasProduct = null;
    }


    public static function create()
    {
        return new static();
    }

    public function setCard(ProductCardObject $card)
    {
        $this->card = $card;
        return $this;
    }

    public function addProduct(ProductObject $product, ProductAttributeObject $attr = null, ProductAttributeValueObject $value = null)
    {
        $this->products[] = [$product, $attr, $value];
        return $this;
    }

    public function bindToShop(ShopHasProductObject $has)
    {

        return $this;
    }

    public function bindCardToCategory(CategoryObject $category)
    {

        return $this;
    }

    public function bindCardToShop(ShopHasProductCardObject $has)
    {

        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function save(array &$results = [])
    {
        $this->check();


        //--------------------------------------------
        // CARD - CARD_LANG
        //--------------------------------------------
        /**
         * Saving card and cardLang, BUT,
         * We don't want to create another card for nothing.
         * So, if the card doesn't need to be created, you'll have this:
         * array(1) {
         *      ["ek_product_card_lang"] => array(2) {
         *          ["product_card_id"] => string(4) "3554"
         *          ["lang_id"] => string(1) "1"
         *      }
         * }
         */
        $idCard = null;
        $cardLang = $this->card->getProductCardLang();
        if (null !== $cardLang) { // cardLang is set

            $idCard = $cardLang->getProductCardId();
            if (null === $idCard) {
                /**
                 * The product card doesn't have the cardId set yet,
                 * so we save the card (and the bound cardLang in turn)
                 */
                $r = [];
                $idCard = $this->card->save($r);
                $this->saveResult($r, $results);
            } else {
                /**
                 * cardId already exists, we just save the cardLang
                 */
                $r = [];
                $cardLang->save($r);
                $this->saveResult($r, $results);
                $idCard = $cardLang->getProductCardId();
            }
        } else {
            $r = [];
            $idCard = $this->card->save($r);
            $this->saveResult($r, $results);
        }


        //--------------------------------------------
        // PRODUCT - PRODUCT_LANG - PRODUCT ATTRIBUTES
        //--------------------------------------------
        foreach ($this->products as $productInfo) {
            /**
             * @var $product ProductObject
             */
            list($product, $attr, $value) = $productInfo;
            $r = [];
            a("save product, cardId=$idCard");
            $product->setProductCardId($idCard);
            $product->save($r);
            $this->saveResult($r, $results);
        }
        a($idCard);


    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function error($msg)
    {
        throw new \Exception($msg);
    }
    //--------------------------------------------
    //
    //--------------------------------------------
    private function check()
    {
        if (null === $this->card) {
            $this->error("The card must be set");
        }
        if (empty($this->products)) {
            $this->error("At least one product must be added");
        }
    }

    private function saveResult(array $r, array &$allSavedResults)
    {
        $allSavedResults = array_merge($allSavedResults, $r);
    }
}