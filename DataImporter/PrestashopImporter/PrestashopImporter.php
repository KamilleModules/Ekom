<?php


namespace Module\Ekom\DataImporter\PrestashopImporter;

use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

/**
 *
 * Importer for data from prestashop 1.6
 *
 *
 * Synopsis:
 *
 * - importProducts (we import them first so that we have the reference ready for subsequent calls)
 * - importAttributes
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
     * @param callable|null
     *          bool  $filterByGroupName ( groupName )
     *          if return false, the entry will be ignored.
     */
//    public function importAttributes(callable $filterByGroupName = null)
//    {
//
//        $this->check();
//
//
//        $attrLayer = EkomApi::inst()->attributeLayer();
//        $attrLayer->getAvailableAttributeByCategoryId()
//
//        $db = $this->dbSrc;
//        $rows = QuickPdo::fetchAll("
//select id_attribute_group, name
//from $db.ps_attribute_group_lang
//        ");
//
//        foreach ($rows as $row) {
//            $groupId = $row['id_attribute_group'];
//
//
//            if (null !== $filterByGroupName && false === call_user_func($filterByGroupName, $row['name'])) {
//                continue;
//            }
//
//            $rowsAttr = QuickPdo::fetchAll("
//select al.id_attribute, al.name
//from $db.ps_attribute a
//inner join $db.ps_attribute_lang al on al.id_attribute=a.id_attribute
//
//where a.id_attribute_group=$groupId
//            ");
//
//
//
//            a($row);
//            a($rowsAttr);
//
//        }
//
//    }

    public function importProducts()
    {
        $this->check();


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
        $rows = QuickPdo::fetchAll("
select 
p.id_product, 
p.price, 
p.reference,        
p.weight,        
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
        $cardApi = EkomApi::inst()->productCard();





        foreach ($rows as $row) {
            $id_product = $row['id_product'];
            a($row['id_product']);


            $imageTypes = [
                '-cart_default2x.jpg',
                '-home_default2x.jpg',
                '-large_default2x.jpg',
                '-medium_default2x.jpg',
            ];


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



            $productId = $productApi->create([
                "reference" => $row['reference'],
                "weight" => $row['reference'],
            ]);


            $attrRows = QuickPdo::fetchAll("
select 
pa.reference as price_variation,
pa.price as price_variation,
pa.weight as weight_variation,
            
            ");

            foreach ($imageTypes as $suffix) {
                $imgSrc = $this->imgDirSrc . "/" . $this->hash($id_product) . '/' . $id_product . $suffix;
                if (true === file_exists($imgSrc)) {
                    a($imgSrc);

                    $imgTarget = $imgSrc;


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
}