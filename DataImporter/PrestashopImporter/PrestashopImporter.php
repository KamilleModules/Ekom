<?php


namespace Module\Ekom\DataImporter\PrestashopImporter;

use ArrayToString\ArrayToStringTool;
use Bat\CaseTool;
use Bat\FileSystemTool;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;

/**
 *
 * Importer for data from prestashop 1.6
 *
 *
 *
 *
 * ----------------
 * BEWARE:
 * THIS DOES NOT IMPORT MULTIPLE SHOPS AND OR LANGS.
 * ----------------
 * Note: it assumes that you have only one shop in prestashop
 * and only one shop (with id=1) and one lang (with id=1) in ekom.
 * ----------------
 *
 *
 *
 *
 *
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
 * ps_image
 * ---------------
 * - id_image
 * - id_product
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
    private $mapTaxRulesGroups;


    public function __construct()
    {
        $this->mapTaxRulesGroups = [];
    }


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
     * Dans prestashop:
     *
     * - ps_supplier
     *      - id_supplier
     *      - name
     *      - date_add
     *      - date_upd
     *      - active
     * - ps_supplier_lang
     *      - id_supplier
     *      - id_lang
     *      - description
     *      - meta_title
     *      - meta_keywords
     *      - meta_description
     * - ps_supplier_shop
     *      - id_supplier
     *      - id_shop
     *
     * Dans ekom:
     * - ek_provider
     *      - id
     *      - shop_id
     *      - name
     *
     *
     */
    public function importSuppliers()
    {

        $db = $this->dbSrc;
        $shopId = 1;


        $rows = QuickPdo::fetchAll("
select 
s.id_supplier,
s.name
from $db.ps_supplier s    
        ");


        $provider = EkomApi::inst()->provider();
        foreach ($rows as $row) {
            $provider->push([
                "id" => $row['id_supplier'],
            ], [
                "shop_id" => $shopId,
                "name" => $row['name'],
                "id" => $row['id_supplier'],
            ]);
        }
    }


    /**
     * Dans prestashop:
     *
     * - ps_manufacturer
     *      - id_manufacturer
     *      - name
     *      - date_add
     *      - date_upd
     *      - active
     * - ps_manufacturer_lang
     *      - id_manufacturer
     *      - id_lang
     *      - description
     *      - short_description
     *      - meta_title
     *      - meta_keywords
     *      - meta_description
     * - ps_manufacturer_shop
     *      - id_manufacturer
     *      - id_shop
     *
     * Dans ekom:
     * - ek_provider
     *      - id
     *      - shop_id
     *      - name
     *
     *
     */
    public function importManufacturers()
    {

        $db = $this->dbSrc;
        $shopId = 1;


        $rows = QuickPdo::fetchAll("
select 
m.id_manufacturer,
m.name
from $db.ps_manufacturer m    
        ");


        $manufacturer = EkomApi::inst()->manufacturer();
        foreach ($rows as $row) {
            $manufacturer->push([
                "id" => $row['id_manufacturer'],
                "shop_id" => $shopId,
            ], [
                "shop_id" => $shopId,
                "name" => $row['name'],
                "id" => $row['id_manufacturer'],
            ]);
        }
    }


    /**
     * Dans prestashop:
     *
     * ps_tag
     *      - id_tag
     *      - id_lang
     *      - name
     *
     * ek_tag
     *
     *
     *
     */
    public function importTags()
    {
        $db = $this->dbSrc;
        $shopId = 1;


        $rows = QuickPdo::fetchAll("
select *
from $db.ps_tag    
        ");


        $tag = EkomApi::inst()->tag();
        foreach ($rows as $row) {
            $tag->push([
                "id" => $row['id_tag'],
                "lang_id" => $row['id_lang'],
            ], [
                "id" => $row['id_tag'],
                "name" => $row['name'],
                "lang_id" => $row['id_lang'],
            ]);
        }
    }


    /**
     * Dans prestashop:
     *
     * ps_tax_rules_group
     *      - id_tax_rules_group
     *      - name
     *      - active
     *      - deleted
     *      - date_add
     *      - date_upd
     *
     * Dans ekom:
     *
     * ek_tax_group
     *
     *
     *
     *
     *
     * @param $map , array of ps_tax_rules_group.id_tax_rules_group => ek_tax_group.id
     * @return $this
     *
     */
    public function setTaxRulesGroupMap(array $map)
    {
        $this->mapTaxRulesGroups = $map;
        return $this;
    }


    /**
     * Features in prestashop
     * ==========================
     * - ps_feature
     *      - id_feature
     *      - position
     *
     * - ps_feature_lang
     *      - id_feature
     *      - id_lang
     *      - name
     *
     * - ps_feature_product
     *      - id_feature
     *      - id_product
     *      - id_feature_value
     *
     * - ps_feature_shop
     *      - id_feature
     *      - id_shop
     *
     * - ps_feature_value
     *      - id_feature_value
     *      - id_feature
     *      - custom (I don't know what that is)
     *
     * - ps_feature_value_lang
     *      - id_feature_value
     *      - id_lang
     *      - value
     *
     *
     * - ps_product
     *      - ...
     *      - id_product
     *      - reference
     *
     *
     *
     */
    public function importFeatures(array $options = [])
    {

        $options = array_merge([
            'ekomShopId' => 1,
            'prestaShopId' => 1,
            'ekomLangId' => 1,
            'prestaLangId' => 1,
        ], $options);


        $db = $this->dbSrc;
        $db2 = $this->dbTarget;
        $prestaShopId = (int)$options['prestaShopId'];
        $ekomShopId = (int)$options['ekomShopId'];
        $prestaLangId = (int)$options['prestaLangId'];
        $ekomLangId = (int)$options['ekomLangId'];

        $features = QuickPdo::fetchAll("
select 
f.position,
fl.id_feature,
fl.name

from $db.ps_feature f
inner join $db.ps_feature_lang fl on fl.id_feature=f.id_feature
inner join $db.ps_feature_shop s on s.id_feature=f.id_feature      
  
where fl.id_lang=$prestaLangId  
and s.id_shop=$prestaShopId
  
        ");


//        a($features);
        $featureApi = EkomApi::inst()->feature();
        $featureLangApi = EkomApi::inst()->featureLang();
        $featureValueApi = EkomApi::inst()->featureValue();
        $featureValueLangApi = EkomApi::inst()->featureValueLang();
        $proHasApi = EkomApi::inst()->productHasFeature();


        $featureApi->deleteAll();
        $featureValueApi->deleteAll();
        foreach ($features as $feature) {


            $position = $feature['position'];

            $featureId = $featureApi->create([]);
            $featureLangApi->create([
                "feature_id" => $featureId,
                "lang_id" => $ekomLangId,
                "name" => $feature['name'],
            ]);


            $values = QuickPdo::fetchAll("
select 
vl.id_feature_value,
vl.`value`
     
from $db.ps_feature_value v
inner join $db.ps_feature_value_lang vl on vl.id_feature_value=v.id_feature_value

where v.id_feature=$featureId
and vl.id_lang=$prestaLangId
            
            ");


            $id_feature = $feature['id_feature'];


            foreach ($values as $row) {
                $valueId = $featureValueApi->create([
                    'feature_id' => $featureId,
                ]);

                $featureValueLangApi->create([
                    'feature_value_id' => $valueId,
                    'lang_id' => $ekomLangId,
                    'value' => $row["value"],
                ]);


                $id_feature_value = $row['id_feature_value'];
                $products = QuickPdo::fetchAll("
select
fp.id_feature,
fp.id_product,
fp.id_feature_value,
p.reference

from $db.ps_feature_product fp 
inner join $db.ps_product p on p.id_product=fp.id_product
where fp.id_feature=$id_feature
and fp.id_feature_value=$id_feature_value


                ");

                foreach ($products as $product) {
                    $ref = $product['reference'];
                    if (false !== ($pro = QuickPdo::fetch("
select id from $db2.ek_product where reference=:ref                    
                    ", [
                            'ref' => $ref,
                        ]))
                    ) {
                        $ekomProductId = $pro['id'];
                        try {

                            $proHasApi->create([
                                'product_id' => $ekomProductId,
                                'feature_id' => $featureId,
                                'shop_id' => $ekomShopId,
                                'feature_value_id' => $featureId,
                                'position' => $position,
                            ]);
                        } catch (\PDOException $e) {
                            if (false === QuickPdoExceptionTool::isDuplicateEntry($e)) {
                                throw $e;
                            }
                        }
                    }
                }

            }


        }

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
     * Nomenclature
     * Dans prestashop:
     *
     * - ps_product_attribute représente une déclinaison de produit
     * - ps_attribute représente un attribut (0.5kg, 1kg, 1.5kg, ...)
     * - ps_attribute_group représente un groupe d'attribut (diamètre, pointure, taille, couleur, ...)
     *
     *
     *
     *
     * -----------
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
     * @return void
     * @throws \Exception
     */
    public function importAttributes()
    {
        $this->check();
        $attr = EkomApi::inst()->productAttribute();
        $attrLang = EkomApi::inst()->productAttributeLang();
        $attrValue = EkomApi::inst()->productAttributeValue();
        $attrValueLang = EkomApi::inst()->productAttributeValueLang();

        $langId = 1;


        $attr->deleteAll();
        $attrValue->deleteAll();


        $db = $this->dbSrc;
        $rows = QuickPdo::fetchAll("
select id_attribute_group, name
from $db.ps_attribute_group_lang
        ");


        foreach ($rows as $row) {
            $groupId = $row['id_attribute_group'];
            $groupName = $row['name'];


            $attr->push([
                "id" => $groupId,
            ], [
                "id" => $groupId,
                "name" => $groupName,
            ]);


            $attrLang->push([
                "product_attribute_id" => $groupId,
                "lang_id" => $langId,
            ], [
                "product_attribute_id" => $groupId,
                "lang_id" => $langId,
                "name" => $groupName,
            ]);


            $rowsAttr = QuickPdo::fetchAll("
select al.id_attribute, al.name
from $db.ps_attribute a
inner join $db.ps_attribute_lang al on al.id_attribute=a.id_attribute

where a.id_attribute_group=$groupId
            ");


            foreach ($rowsAttr as $rowAttr) {
                $attrName = $rowAttr['name'];
                $attrId = $rowAttr['id_attribute'];


                try {

                    $attrValue->push([
                        "id" => $attrId,
                    ], [
                        "id" => $attrId,
                        "value" => $attrName,
                    ]);

                    $attrValueLang->push([
                        "product_attribute_value_id" => $attrId,
                        "lang_id" => $langId,
                    ], [
                        "product_attribute_value_id" => $attrId,
                        "lang_id" => $langId,
                        "value" => $attrName,
                    ]);
                } catch (\Exception $e) {
                    a("insert failed for ");
                    a($rowAttr);
                }

            }


        }


    }


    public function importAttributesOld(callable $filterByGroupName = null, $langId = null, $memoryFile = null)
    {

        $this->check();

        EkomApi::inst()->productAttribute()->deleteAll();
        EkomApi::inst()->productAttributeValue()->deleteAll();


        if (null === $langId) {
            
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


            $attrName = $this->toSnake($row['name']);


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

                $value = $this->toSnake($rowAttr['name']);

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
     *
     * You should call importAttributes prior to call this method.
     * Otherwise the results are unpredictable.
     *
     *
     *
     * Dans prestashop:
     *
     * ps_product
     * - id_product
     * - id_supplier
     * - id_manufacturer
     * - id_category_default
     * - id_shop_default
     * - id_tax_rules_group
     * - on_sale
     * - online_only
     * - ean13
     * - upc
     * - ecotax
     * - quantity
     * - minimal_quantity
     * - price
     * - wholesale_price
     * - unity
     * - unity_price_ratio
     * - additional_shipping_cost
     * - reference
     * - supplier_reference
     * - location
     * - width
     * - height
     * - depth
     * - weight
     * - out_of_stock
     * - quantity_discount
     * - customizable
     * - uploadable_files
     * - text_fields
     * - active
     * - redirect_type
     * - id_product_redirected
     * - available_for_order
     * - available_date
     * - condition
     * - show_price
     * - indexed
     * - visibility
     * - cache_is_pack
     * - cache_has_attachments
     * - is_virtual
     * - cache_default_attribute
     * - date_add
     * - date_upd
     * - advanced_stock_management
     * - pack_stock_type
     *
     *
     * ps_product_lang
     * - id_product
     * - id_shop
     * - id_lang
     * - description
     * - description_short
     * - link_rewrite
     * - meta_description
     * - meta_keywords
     * - meta_title
     * - name
     * - available_now
     * - available_later
     *
     *
     * ps_product_tag
     * - id_product
     * - id_tag
     * - id_lang
     *
     *
     * ps_stock_available (contient les quantités)
     * - id_stock_available
     * - id_product
     * - id_product_attribute
     * - id_shop
     * - id_shop_group
     * - quantity
     * - depends_on_stock
     * - out_of_stock
     *
     *
     *
     */
    public function importProducts()
    {
        $db = $this->dbSrc;
        $db2 = $this->dbTarget;
        $langId = 1;
        $shopId = 1;
        $sellerId = 1;
        $productTypeId = 1;

        $rows = QuickPdo::fetchAll("
select 
p.id_product, 
p.id_supplier, 
p.id_manufacturer, 
p.id_tax_rules_group, 
p.ean13, 
p.quantity, 
p.price, 
p.wholesale_price, 
p.reference,        
p.width,        
p.height,        
p.depth,        
p.weight,        
date(p.date_add) as date_add_date,        
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
            /**
             * 2 types de produits dans prestashop:
             * - déclinaison
             * - produit sans déclinaison
             *
             */

            $id_product = $row['id_product'];

//            a($row['id_product']);


            /**
             * déclinaisons
             */
            $attrRows = QuickPdo::fetchAll("
select
pa.id_product_attribute,
pa.reference as reference,
pa.ean13,
pa.price as price_variation,
pa.quantity,
pa.weight as weight_variation

from $db.ps_product_attribute pa
where pa.id_product=$id_product


            ");


            /**
             * Insertion d'un produit dans ekom.
             * Tables impactées:
             *
             * - ek_product_card
             * - ek_product_card_lang
             * - ek_shop_has_product_card
             * - ek_shop_has_product_card_lang
             *
             * - ek_product
             * - ek_product_lang
             * - ek_shop_has_product
             * - ek_shop_has_product_lang
             *
             *
             * Features
             * - ek_product_has_feature
             *
             * Tags
             * - ek_shop_has_product_has_tag
             *
             *
             *
             *
             */


            //--------------------------------------------
            // INSERT PRODUCT_CARD AND PRODUCT_CARD_LANG
            //--------------------------------------------
            $cardId = $cardApi->create([]);


            /**
             * This slug will be used to access the product card
             */
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


            //--------------------------------------------
            // NOW INSERT PRODUCT(S) AND PRODUCT_LANG
            //--------------------------------------------
            $originalWeight = $row['weight'];
            $originalPrice = $row['price'];
            $isNovelty = (date("Y-m-d", time() - 30 * 86400) < $row['date_add_date']);
            $codes = [];
            if (true === $isNovelty) {
                $codes[] = "n";
            }
            $sCodes = implode(",", $codes);


            $productId = null;
            if (count($attrRows) > 0) {
                /**
                 * Si le produit contient des déclinaisons,
                 * chaque déclinaison devient un produit dans ekom,
                 * et toutes les déclinaisons sont associées à la même card.
                 */


                foreach ($attrRows as $attrRow) {



                    $idProductAttribute = $attrRow['id_product_attribute'];
                    $weight = $originalWeight + $attrRow['weight_variation'];
                    $price = $originalPrice + $attrRow['price_variation'];
                    $reference = $attrRow['reference'];
                    $quantity = (int)QuickPdo::fetch("
select
quantity
from $db.ps_stock_available 
where id_product=$id_product
and id_product_attribute=$idProductAttribute
            ", [], \PDO::FETCH_COLUMN);


                    $productId = $productApi->create([
                        "reference" => $reference,
                        "weight" => $weight,
                        "price" => $price,
                        "product_card_id" => $cardId,
                        "width" => $row['width'],
                        "height" => $row['height'],
                        "depth" => $row['depth'],
                    ]);
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
                        "quantity" => $quantity,
                        "active" => "1",
                        "_discount_badge" => "",
                        "seller_id" => $sellerId,
                        "product_type_id" => $productTypeId,
                        "reference" => $reference,
                        "_popularity" => 0,
                        "codes" => $sCodes,
                        "manufacturer_id" => $row["id_manufacturer"],
                        "ean" => $attrRow["ean13"],
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


                    /**
                     * Chaque combinaison a une combinaison unique d'attributs dans prestashop.
                     * Pour rappel, voici la logique de prestashop:
                     *
                     * - id_product_attribute représente une déclinaison de produit.
                     *
                     * La table id_product_attribute indique les déclinaisons pour un produit donné.
                     * Chaque déclinaison de produit peut être liée à un ou plusieurs attributs
                     * (dans la compagnie dans laquelle je travaille, aucun produit n'utilise plus d'un attribut).
                     *
                     * La table ps_product_attribute_combination lie une déclinaison à l'ensemble des attributs
                     * que celle-ci utilise (id_product_attribute <--> id_attribute).
                     *
                     * Chaque attribut appartient à un groupe ps_attribute_group.
                     *
                     *
                     * Ci-dessous, nous transférons les attributs de prestashop vers ekom.
                     *
                     */




                    $rowsAttributes = QuickPdo::fetchAll("
select 
a.id_attribute,
a.id_attribute_group
from ps_product_attribute_combination c
inner join ps_attribute a on a.id_attribute=c.id_attribute
where c.id_product_attribute=$idProductAttribute 
                    ");

                    if($rowsAttributes){
                        foreach($rowsAttributes as $rowAttribute){
                            $attributeId = $rowAttribute['id_attribute'];
                            $attributeGroupId = $rowAttribute['id_attribute_group'];


                            $productHasAttributeApi->create([
                                "product_id" => $productId,
                                "product_attribute_id" => $attributeGroupId,
                                "product_attribute_value_id" => $attributeId,
                                "order" => 0,
                            ]);


                        }
                    }
                }
            }
            else {

                /**
                 * C'est le cas où le produit ne contient pas de déclinaisons,
                 * on insère le produit et la carte (container) qui le contient.
                 */


                $quantity = (int)QuickPdo::fetch("
select
quantity
from $db.ps_stock_available 
where id_product=$id_product
            ", [], \PDO::FETCH_COLUMN);


                $productId = $productApi->create([
                    "reference" => $row['reference'],
                    "weight" => $row['weight'],
                    "price" => $row['price'],
                    "product_card_id" => $cardId,
                    "width" => $row['width'],
                    "height" => $row['height'],
                    "depth" => $row['depth'],
                ]);
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
                    "quantity" => $quantity,
                    "active" => "1",
                    "_discount_badge" => "",
                    "seller_id" => $sellerId,
                    "product_type_id" => $productTypeId,
                    "reference" => $row['reference'],
                    "_popularity" => 0,
                    "codes" => $sCodes,
                    "manufacturer_id" => $row["id_manufacturer"],
                    "ean" => $row["ean13"],
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

            }




            $taxGroupId = $this->getTaxGroupIdByTaxRulesGroupId($row['id_tax_rules_group']);
            $shopHasCardApi->create([
                "shop_id" => $shopId,
                "product_card_id" => $cardId,
                "product_id" => $productId,
                "tax_group_id" => $taxGroupId,
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


            //--------------------------------------------
            // IMAGES
            //--------------------------------------------
            $label = $this->toSnake($row['label']);
            $imageIds = QuickPdo::fetchAll("
select id_image from $db.ps_image where id_product=$id_product         
            ", [], \PDO::FETCH_COLUMN);

            foreach ($imageIds as $imageId) {
                foreach ($imageTypes as $suffix => $ekomType) {
                    $imgSrc = $this->imgDirSrc . "/" . $this->hash($imageId) . '/' . $imageId . $suffix;
                    if (true === file_exists($imgSrc)) {
                        $imgTarget = $this->imgDirTarget . "/" . $this->hash($cardId) . "/$ekomType/" . $label . '-' . $imageId . '.jpg';
                        FileSystemTool::copyFile($imgSrc, $imgTarget);
                    }
                }
            }
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
    public function importProductsOld($memoryFile, $shopId = null, $langId = null)
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
            
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }
        $shopId = (int)$shopId;

        if (null === $langId) {
            
            $langId = ApplicationRegistry::get("ekom.lang_id");
        }
        $langId = (int)$langId;


        EkomApi::inst()->product()->deleteAll();
        EkomApi::inst()->productCard()->deleteAll();
        FileSystemTool::clearDir($this->imgDirTarget, true, false);


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

//            if(459 !== (int)$row['id_product']){
//                continue;
//            }


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


//            a($attrRows);
//            a($row);
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

                    $productLangApi->create([
                        "product_id" => $productId,
                        "lang_id" => $langId,
                        "label" => $row['label'],
                        "description" => $row['description'],
                        "meta_title" => $row['meta_title'],
                        "meta_description" => $row['meta_description'],
                        "meta_keywords" => $row['meta_keywords'],
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

            }


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


            //--------------------------------------------
            // IMAGES
            //--------------------------------------------
            $label = $this->toSnake($row['label']);
            $imageIds = QuickPdo::fetchAll("
select id_image from $db.ps_image where id_product=$id_product         
            ", [], \PDO::FETCH_COLUMN);

            foreach ($imageIds as $imageId) {
                foreach ($imageTypes as $suffix => $ekomType) {
                    $imgSrc = $this->imgDirSrc . "/" . $this->hash($imageId) . '/' . $imageId . $suffix;
                    if (true === file_exists($imgSrc)) {
                        $imgTarget = $this->imgDirTarget . "/" . $this->hash($cardId) . "/$ekomType/" . $label . '-' . $imageId . '.jpg';
                        FileSystemTool::copyFile($imgSrc, $imgTarget);
                    }
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


    private function toSnake($str)
    {
        $str = str_replace([',', '.'], ' ', $str);
        $ret = CaseTool::toSnake($str);
        return $ret;
    }

    private function getTaxGroupIdByTaxRulesGroupId($id)
    {
        if (array_key_exists($id, $this->mapTaxRulesGroups)) {
            return $this->mapTaxRulesGroups[$id];
        }
        return null;
    }
}