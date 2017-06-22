<?php


namespace Module\Ekom\DataImporter\PrestashopImporter;

use ArrayToString\ArrayToStringTool;
use Bat\CaseTool;
use Bat\FileSystemTool;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

/**
 *
 * Importer for data from prestashop 1.6
 *
 *
 * Synopsis:
 *
 * - importAttributes
 * - importProducts (we import them first so that we have the reference ready for subsequent calls)
 *
 *
 *
 * Products are stored in the ps_product table.
 * Correspondence between products and their "variations" are stored in the ps_product_attribute table.
 * Correspondence between a variation and the attribute that characterizes it is in the ps_product_attribute_combination table.
 *
 *
 *
 * ps_product
 * --------------
 * - id_product
 * - price            decimal(20,6)
 * - reference
 * - weight         decimal(20,6)
 *
 *
 * ps_product_attribute
 * ----------------------
 * - id_product
 * - id_product_attribute
 * - reference (of the variation)
 * - price          decimal(20,6) variation
 * - weight         decimal(20,6) variation
 *
 *
 *
 * ps_product_attribute_combination
 * -----------------------------------
 * - id_attribute
 * - id_product_attribute
 *
 *
 * ps_attribute
 * ---------------
 * - id_attribute
 * - id_attribute_group
 *
 *
 * ps_attribute_lang
 * ---------------
 * - id_attribute
 * - name   (like S, M, L, Taille unique, Gris, Taupe, Beige, ...)
 *
 *
 * ps_attribute_group_lang
 * ---------------
 * - id_attribute_group
 * - name   (like Taille, Pointure, Couleur, ...)
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
class PrestashopImporter
{
    private $dbSrc;
    private $dbTarget;
    private $imgDirSrc;
    private $imgDirTarget;


    public static function create()
    {
        return new static();
    }

    public function setDatabases($dbSrc, $dbTarget)
    {
        $this->dbSrc = $dbSrc;
        $this->dbTarget = $dbTarget;
        return $this;
    }

    public function setImageDirs($imgDirSrc, $imgDirTarget)
    {
        $this->imgDirSrc = $imgDirSrc;
        $this->imgDirTarget = $imgDirTarget;
        return $this;
    }


    /**
     * If your new application has new cats, then how do you map your old products to the new cats?
     * A quick'n'dirty way is to map them randomly using the randomCatHasProduct method.
     *
     * This function will take all products in your ekom database and assign them to one leaf
     * category (category with no children).
     *
     */
    public function randomCatHasProduct(callable $leafFilter = null)
    {
        $noChildrenCatIds = EkomApi::inst()->categoryLayer()->getLeafCategoryIds();
        if (null !== $leafFilter) {
            $noChildrenCatIds = array_filter($noChildrenCatIds, $leafFilter);
        }

        $count = count($noChildrenCatIds) - 1;

        $catHasCardApi = EkomApi::inst()->categoryHasProductCard();

        $rows = QuickPdo::fetchAll("select id from ek_product_card");
        foreach ($rows as $row) {
            $cardId = $row['id'];
            $catHasCardApi->create([
                "category_id" => $noChildrenCatIds[rand(0, $count)],
                "product_card_id" => $cardId,
            ]);
        }

    }

    /**
     *
     * Take the attribute groups and attributes from prestashop
     * and import them into product attributes and product attribute values in ekom.
     *
     * Also create a memory file containing the map between prestashop attribute group id to ekom product attribute id,
     * and prestashop attribute id to ekom product attribute value id.
     *
     * This file is used by the importProduct method.
     *
     *
     *
     *
     * @param callable|null
     *          bool  $filterByGroupName ( groupName )
     *          if return false, the entry will be ignored.
     */
    public function importAttributes(callable $filterByGroupName = null, $langId = null, $memoryFile = null)
    {

        $this->check();

        EkomApi::inst()->productAttribute()->deleteAll();
        EkomApi::inst()->productAttributeValue()->deleteAll();


        if (null === $langId) {
            EkomApi::inst()->initWebContext();
            $langId = ApplicationRegistry::get("ekom.lang_id");
        }
        $langId = (int)$langId;


        $attrApi = EkomApi::inst()->productAttribute();
        $attrLangApi = EkomApi::inst()->productAttributeLang();

        $attrValueApi = EkomApi::inst()->productAttributeValue();
        $attrValueLangApi = EkomApi::inst()->productAttributeValueLang();


        /**
         * array of presta.id_attribute_group => ekom.product_attribute.id
         */
        $memoAttr = [];
        /**
         * array of presta.id_attribute => ekom.product_attribute_value.id
         */
        $memoValues = [];


        $db = $this->dbSrc;
        $rows = QuickPdo::fetchAll("
select id_attribute_group, name
from $db.ps_attribute_group_lang
        ");

        foreach ($rows as $row) {
            $groupId = $row['id_attribute_group'];


            if (null !== $filterByGroupName && false === call_user_func($filterByGroupName, $row['name'])) {
                continue;
            }

            $rowsAttr = QuickPdo::fetchAll("
select al.id_attribute, al.name
from $db.ps_attribute a
inner join $db.ps_attribute_lang al on al.id_attribute=a.id_attribute

where a.id_attribute_group=$groupId
            ");


            $attrName = CaseTool::toSnake($row['name']);


            $exist = false;
            if (false === ($attrId = $attrApi->readColumn("id", [
                    ["name", "=", $attrName],
                ]))
            ) {
                $attrId = $attrApi->create([
                    'name' => $attrName,
                ]);
            } else {
                $exist = true;
            }

            $memoAttr[$row['id_attribute_group']] = $attrId;

            if (false === $exist) {
                $attrLangApi->create([
                    "product_attribute_id" => $attrId,
                    "lang_id" => $langId,
                    "name" => $row['name'],
                ]);
            }

            foreach ($rowsAttr as $rowAttr) {

                $exist = false;

                $value = CaseTool::toSnake($rowAttr['name']);

                if (false === ($attrValueId = $attrValueApi->readColumn("id", [
                        ["value", "=", $value],
                    ]))
                ) {
                    $attrValueId = $attrValueApi->create([
                        'value' => $value,
                    ]);
                } else {
                    $exist = true;
                }

                $memoValues[$rowAttr['id_attribute']] = $attrValueId;

                if (false === $exist) {
                    $attrValueLangApi->create([
                        "product_attribute_value_id" => $attrValueId,
                        "lang_id" => $langId,
                        "value" => $rowAttr['name'],
                    ]);
                }
            }
        }


        if (null !== $memoryFile) {
            $data = '<?php ' . PHP_EOL;
            $data .= '$attr = ' . ArrayToStringTool::toPhpArray($memoAttr, true) . ';' . PHP_EOL;
            $data .= PHP_EOL;
            $data .= '$values = ' . ArrayToStringTool::toPhpArray($memoValues, true) . ';' . PHP_EOL;
            FileSystemTool::mkfile($memoryFile, $data);
        }

    }


    /**
     * Translate prestashop product to ekom product cards,
     * and prestashop product_attributes to ekom products.
     *
     * It also partially handles attributes.
     *
     * This method works along with the importAttribute method (which should be called first)
     *
     * This method also copies images from one system to another (presta => ekom).
     *
     *
     *
     *
     * Note: this version only works if the prestashop have a maximum of one attribute per product.
     *
     *
     */
    public function importProducts($memoryFile, $shopId = null, $langId = null)
    {
        $this->check();

        if (!file_exists($memoryFile)) {
            throw new \Exception("memory file not existing, call importAttributes first");
        }

        $attr = [];
        $values = [];
        include $memoryFile;
        $memoAttributes = $attr;
        $memoValues = $values;


        if (null === $shopId) {
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }
        $shopId = (int)$shopId;

        if (null === $langId) {
            EkomApi::inst()->initWebContext();
            $langId = ApplicationRegistry::get("ekom.lang_id");
        }
        $langId = (int)$langId;


        EkomApi::inst()->product()->deleteAll();
        EkomApi::inst()->productCard()->deleteAll();


        /**
         * https://www.quora.com/How-can-I-change-a-page-title-and-meta-tag-dynamic-using-JavaScript-or-jQuery-or-AJAX-in-C
         * https://stackoverflow.com/questions/30588501/seo-affected-changing-title-tag-by-javascript
         */

        /**
         * price => ps_product.price
         * reference => ps_product.reference
         * weight => ps_product.weight
         * description => ps_product_lang.description_short
         * slug => ps_product_lang.link_rewrite
         * meta_description => ps_product_lang.meta_description
         * meta_keywords => ps_product_lang.meta_keywords
         * meta_title => ps_product_lang.meta_title
         * label => ps_product_lang.name
         *
         *
         *
         * images in prestashop:
         *      product_id is hashed and different variations are available.
         *      For instance id_product 1520 =>
         *
         *      p/1/5/2/0/1520-$identifier.jpg
         *
         *
         *
         * $identifier takes all the following values:
         * - cart_default2x             (160x160)       -> thumb
         * - home_default2x             (500x500)       -> medium
         * - large_default2x            (916x916)       -> large
         * - medium_default2x           (250x250)       -> small
         * - small_default2x            (196x196)
         * - thickbox_default2x         (1600x1600)     -> original
         *
         *
         *
         *
         * In ekom, the product id identifies a product, so for instance product id=1520
         * will have its own directory:
         *
         * cards/1/5/2/0/
         *      which contains the following:
         *      - large: contains any number of files
         *      - medium: contains any number of files
         *      - small: contains any number of files
         *      - thumb: contains any number of files
         *
         *
         * However, the name must be the same across all directories, so for instance: kettle-bell-05.jpg
         * (same name under the large, medium, small and thumb folders).
         *
         * More info in the class-modules/Ekom/Api/Layer/ImageLayer.php class.
         *
         *
         *
         * ps_product_attribute:
         * - id_product_attribute
         * - id_product
         * - reference
         * - price
         * - quantity
         * - weight: a relative offset compared to the product's weight
         *
         *
         * ps_attribute:
         * - id_attribute
         *
         * ps_attribute_lang:
         * - id_attribute
         * - name
         *
         *
         *
         * ps_product_attribute_combination
         * - id_attribute
         * - id_product_attribute
         *
         *
         */
        $db = $this->dbSrc;
        $db2 = $this->dbTarget;
        $rows = QuickPdo::fetchAll("
select 
p.id_product, 
p.price, 
p.reference,        
p.weight,        
p.wholesale_price, 
l.description_short as description,        
l.link_rewrite as slug,        
l.meta_title,        
l.meta_description,        
l.meta_keywords,        
l.name as label
      
from $db.ps_product p 
inner join $db.ps_product_lang l on l.id_product=p.id_product
order by p.id_product asc      
      
        
        ");

        $productApi = EkomApi::inst()->product();
        $productLangApi = EkomApi::inst()->productLang();
        $shopHasProductApi = EkomApi::inst()->shopHasProduct();
        $shopHasProductLangApi = EkomApi::inst()->shopHasProductLang();
        $productHasAttributeApi = EkomApi::inst()->productHasProductAttribute();

        $cardApi = EkomApi::inst()->productCard();
        $cardLangApi = EkomApi::inst()->productCardLang();
        $shopHasCardApi = EkomApi::inst()->shopHasProductCard();
        $shopHasCardLangApi = EkomApi::inst()->shopHasProductCardLang();


        $imageTypes = [
            '-cart_default2x.jpg' => "thumb",
            '-home_default2x.jpg' => "medium",
            '-large_default2x.jpg' => "large",
            '-medium_default2x.jpg' => "small",
        ];


        foreach ($rows as $row) {
            $id_product = $row['id_product'];
//            a($row['id_product']);


            /**
             * WARNING!!
             * This is just a helper for my use case, not the perfect
             * prestashop 2 ekom importer.
             *
             * In my case, all products in our prestashop application seem
             * to use at most one product attribute at the time (like size, color, diameter, etc...)
             * but never two (or more) at the same time.
             *
             * Since implementing a product with multiple parallel attributes would take a little more
             * time (and I don't have it yet), this implementation will only fulfill my use case
             * and assume that a product can only have at most one product attribute at the time.
             *
             *
             *
             */


            $attrRows = QuickPdo::fetchAll("
select
pa.reference as reference,
pa.price as price_variation,
pa.weight as weight_variation,
a.id_attribute_group,
a.id_attribute

from $db.ps_product_attribute pa
inner join $db.ps_product_attribute_combination c on c.id_product_attribute=pa.id_product_attribute
inner join $db.ps_attribute a on a.id_attribute=c.id_attribute

where pa.id_product=$id_product


            ");


            // insert product card and products in ekom
            $cardId = $cardApi->create([]);

            $slug = $row['slug'];

            if (false !== ($r = $cardLangApi->readColumn("product_card_id", [
                    ["slug", "=", $row['slug']],
                ]))
            ) {
                $slug .= "_" . rand(1, 1000000);
            }


            $cardLangApi->create([
                "product_card_id" => $cardId,
                "lang_id" => $langId,
                "label" => $row['label'],
                "description" => $row['description'],
                "slug" => $slug,
                "meta_title" => $row['meta_title'],
                "meta_description" => $row['meta_description'],
                /**
                 * all my keywords in presta were empty so I didn't bother,
                 * but if you have keywords to translate, be aware that ekom uses
                 * a serialized array containing keywords.
                 */
                "meta_keywords" => $row['meta_keywords'],
            ]);


            $originalWeight = $row['weight'];
            $originalPrice = $row['price'];


            $productId = null;
            if (count($attrRows) > 0) {


                foreach ($attrRows as $attrRow) {

                    /**
                     * Remember, we assume that we have only ONE argument,
                     * so the logic is very specific and work only in that case.
                     */
                    $weight = $originalWeight + $attrRow['weight_variation'];
                    $price = $originalPrice + $attrRow['price_variation'];

                    $reference = $attrRow['reference'];

                    if (false !== $productApi->readColumn("reference", [
                            ['reference', '=', $reference],
                        ])
                    ) {
                        $reference .= rand(1, 1000000);
                    }


                    $productId = $productApi->create([
                        "reference" => $reference,
                        "weight" => $weight,
                        "price" => $price,
                        "product_card_id" => $cardId,
                    ]);
                    $productAttrId = $this->getAttrDataFromMemo($attrRow['id_attribute_group'], $memoAttributes, "attr");
                    $productValueId = $this->getAttrDataFromMemo($attrRow['id_attribute'], $memoValues, "value");

                    if (
                        false !== $productAttrId &&
                        false !== $productValueId
                    ) {
                        $productHasAttributeApi->create([
                            "product_id" => $productId,
                            "product_attribute_id" => $productAttrId,
                            "product_attribute_value_id" => $productValueId,
                        ]);
                    }

                }
            }
            //
            /**
             * If for some reasons, the presta shop product has no attribute,
             * in ekom every card must have at least one corresponding product
             */
            else {
                $productId = $productApi->create([
                    "reference" => $row['reference'] . '-card-' . rand(1, 1000000),
                    "weight" => $row['weight'],
                    "price" => $row['price'],
                    "product_card_id" => $cardId,
                ]);


            }

            $productLangApi->create([
                "product_id" => $productId,
                "lang_id" => $langId,
                "label" => $row['label'],
                "description" => $row['description'],
                "meta_title" => $row['meta_title'],
                "meta_description" => $row['meta_description'],
                "meta_keywords" => $row['meta_keywords'],
            ]);


            $shopHasProductApi->create([
                "shop_id" => $shopId,
                "product_id" => $productId,
                "price" => null,
                "wholesale_price" => $row["wholesale_price"],
                "quantity" => rand(10, 300), // oops
                "active" => "1",
            ]);


            $shopHasProductLangApi->create([
                "shop_id" => $shopId,
                "product_id" => $productId,
                "lang_id" => $langId,
                "label" => "",
                "description" => "",
                "slug" => "",
                "out_of_stock_text" => "",
                "meta_title" => "",
                "meta_description" => "",
                "meta_keywords" => "",
            ]);


            $shopHasCardApi->create([
                "shop_id" => $shopId,
                "product_card_id" => $cardId,
                "product_id" => $productId,
                "active" => "1",
            ]);
            $shopHasCardLangApi->create([
                "shop_id" => $shopId,
                "product_card_id" => $cardId,
                "lang_id" => $langId,
                "label" => "",
                "description" => "",
                "slug" => "",
                "meta_title" => "",
                "meta_description" => "",
                "meta_keywords" => "",
            ]);


            foreach ($imageTypes as $suffix => $ekomType) {
                $imgSrc = $this->imgDirSrc . "/" . $this->hash($id_product) . '/' . $id_product . $suffix;
                if (true === file_exists($imgSrc)) {
                    $imgTarget = $this->imgDirTarget . "/" . $this->hash($cardId) . "/$ekomType/" . CaseTool::toSnake($row['label']) . '.jpg';
                    FileSystemTool::copyFile($imgSrc, $imgTarget);
                }
            }
        }

    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function check()
    {
        if (null === $this->dbSrc) {
            throw new \Exception("databases not set");
        }
        if (null === $this->imgDirSrc) {
            throw new \Exception("image dirs not set");
        }
    }

    private function hash($string)
    {
        return implode('/', str_split($string, 1));
    }


    private function getAttrDataFromMemo($data, $array, $type = null)
    {
        if (array_key_exists($data, $array)) {
            return $array[$data];
        } else {
            XLog::debug("[Ekom module] - PrestashopImporter: Data not found: $data for type $type");
            return false;
        }
    }
}