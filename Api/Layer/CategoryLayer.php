<?php


namespace Module\Ekom\Api\Layer;


use Bat\CaseTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class CategoryLayer
{


    /**
     * Returns whether or not the $categoryId belongs to (or is) the $ancestorName.
     */
    public function categoryIdBelongsTo($categoryId, $ancestorName, $shopId = null)
    {
        $idAncestor = $this->getCategoryIdByName($ancestorName);
        $treeIds = [];
        $this->collectCategoryIdTreeByCategoryId($treeIds, $categoryId, $shopId);
        return (in_array($idAncestor, $treeIds));
    }

    public function getSlugByName($categoryName)
    {
        return QuickPdo::fetch("
select l.slug 

from ek_category_lang l 
inner join ek_category c on c.id=l.category_id
 
where c.name=:name

", ['name' => $categoryName], \PDO::FETCH_COLUMN);
    }


    public function getCategoryIdByName($name, $shopId=null)
    {
        $shopId = E::getShopId($shopId);
        return QuickPdo::fetch("select id from ek_category where name=:name and shop_id=$shopId", ['name' => $name], \PDO::FETCH_COLUMN);
    }


    /**
     * Collect product card ids contained in the given category and children.
     */
    public function collectProductCardIdsDescendantsByCategoryName(array &$ids, $categoryName, $shopId = null){


        $catId = $this->getCategoryIdByName($categoryName, $shopId);
        $catIds = [];
        $leafIds=[];
        $this->doCollectDescendants($catId, $catIds, $leafIds);
        $ids =  EkomApi::inst()->productCardLayer()->getProductCardIdsByCategoryIds($catIds);
    }

    /**
     * Collect product for product contained in the given category and children.
     */
    public function collectProductCardInfoDescendantsByCategoryName(array &$infos, $categoryName, $shopId = null){


        $catId = $this->getCategoryIdByName($categoryName, $shopId);
        $catIds = [];
        $leafIds=[];
        $this->doCollectDescendants($catId, $catIds, $leafIds);
        $infos =  EkomApi::inst()->productCardLayer()->getProductCardInfosByCategoryIds($catIds);
    }


    /**
     * Collect product ids contained in the given category and ancestors.
     *
     *
     * maxNumber: -1|int, -1 means no limit
     */
    public function collectProductIdsByCategoryName(array &$ids, $categoryName, $maxNumber = 7, $shopId = null)
    {

        $shopId = E::getShopId($shopId);
        $maxNumber = (int)$maxNumber;


        $catIds = [];
        $this->collectCategoryIdTreeByCategoryName($catIds, $categoryName, $shopId);

        if ($catIds) {

            $c = 0;
            while (null !== ($catId = array_shift($catIds))) {

                /**
                 * @todo-ling: Code not tested, can it enter this block and do an infinite loop?
                 */
                if ($c++ > 20) {
                    throw new \Exception("Unexpected infinite loop");
                }


                $q = "
select shpc.product_id from ek_shop_has_product_card shpc 
inner join ek_category_has_product_card chpc on chpc.product_card_id=shpc.product_card_id
inner join ek_category c on c.id=chpc.category_id 

where 
c.id=$catId
and c.shop_id=$shopId 
and shpc.active=1
and shpc.shop_id=$shopId 


            
                ";

                $useLimit = false;
                if (-1 !== $maxNumber) {
                    $q .= "limit 0, $maxNumber";
                    $useLimit = true;
                }

                $newIds = QuickPdo::fetchAll($q, [], \PDO::FETCH_COLUMN);

                foreach ($newIds as $id) {
                    $ids[] = $id;

                    if (true === $useLimit) {
                        $maxNumber--;
                        if ($maxNumber <= 0) {
                            break 2;
                        }
                    }
                }

            }
        }
    }


    public function countProductCards($categoryId)
    {


        return A::cache()->get("Ekom.CategoryLayer.countProductCards.$categoryId", function () use ($categoryId) {

            $catIds = [$categoryId];
            $this->doCollectDescendants($categoryId, $catIds);
            $catIds = array_unique($catIds);

            return QuickPdo::fetch("
select count(product_card_id) as nb from ek_category_has_product_card
where category_id in (" . implode(", ", $catIds) . ")
        
        
        ", [], \PDO::FETCH_COLUMN);
        }, [
            "ek_category",
            "ek_category_has_product_card",
        ]);
    }

    /**
     * Return an array of the ids of the leaf categories (category without children).
     *
     *
     */
    public function getLeafCategoryIds($shopId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $topIds = $this->getTopCategoryIds($shopId);
        $ret = [];
        $leafIds = [];
        foreach ($topIds as $id) {
            $this->doCollectDescendants($id, $ret, $leafIds);
        }
//        foreach ($leafIds as $id) {
//            $r = QuickPdo::fetch("select name from ek_category where id=$id");
//            a("$id: " . $r['name']);
//        }
        return $leafIds;
    }


    /**
     * This method was created for inserting the original categories from a dash file (see Dash2Array tool),
     * where only the label is known.
     * It is a quick'n'dirty tool used to quickly create the original database.
     */
    public function insertCategoryByLabel($label, $parentId = null, $shopId = null, $langId = null)
    {

        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;


        $idCat = 0;
        QuickPdo::transaction(function () use (&$idCat, $parentId, $shopId, $langId, $label) {


            $name = CaseTool::toSnake($label);

            if (false !== ($r = QuickPdo::fetch("select count(1) as count from ek_category where `name`=:zename", ['zename' => $name]))) {
                if ($r['count'] > 0) {
                    $name .= '-' . rand(1, 10000);
                }
            }


            $idCat = EkomApi::inst()->category()->create([
                "name" => $name,
                "category_id" => $parentId,
                "shop_id" => $shopId,
            ]);
            $description = "Description of category $label";

            EkomApi::inst()->categoryLang()->create([
                "category_id" => $idCat,
                "lang_id" => $langId,
                "label" => $label,
                "description" => $description,
                "slug" => $name,
                "meta_title" => $label,
                "meta_description" => $description,
                "meta_keywords" => "$label",
            ]);


        }, function (\Exception $e) {
            throw $e;
        });

        return $idCat;
    }


    /**
     * This method return the id of the category and parent categories.
     */
    public function collectCategoryIdTreeByCategoryId(array &$ids, $categoryId, $shopId = null)
    {
        $api = EkomApi::inst();
        if (null === $shopId) {
            $shopId = E::getShopId();
        }

        return A::cache()->get("Ekom.CategoryLayer.getCategoryIdTreeByCategoryId.$shopId.$categoryId", function () use ($api, $shopId, $categoryId, &$ids) {

            $ids[] = $categoryId;
            $parentCatId = EkomApi::inst()->category()->readColumn("category_id", [
                ["id", "=", $categoryId],
            ]);
            if (null !== $parentCatId) {
                $this->collectCategoryIdTreeByCategoryId($ids, $parentCatId, $shopId);
            }
        }, [
            'ek_product',
            'ek_category',
        ]);
    }


    /**
     * This method return the id of the category and parent categories.
     */
    public function collectCategoryIdTreeByCategoryName(array &$ids, $categoryName, $shopId = null)
    {
        $api = EkomApi::inst();
        if (null === $shopId) {
            $shopId = E::getShopId();
        }

        return A::cache()->get("Ekom.CategoryLayer.getCategoryIdTreeByCategoryId.$shopId.$categoryName", function () use ($api, $shopId, $categoryName, &$ids) {

            $id = EkomApi::inst()->category()->readColumn("id", [
                ["name", "=", $categoryName],
            ]);
            if (false !== $id) {
                $this->collectCategoryIdTreeByCategoryId($ids, $id, $shopId);
            }
        }, [
            'ek_product',
            'ek_category',
        ]);
    }


    /**
     * This method return the id of the product's card categories and parent categories.
     */
    public function getCategoryIdTreeByProductId($productId, $shopId = null, $langId = null)
    {
        $api = EkomApi::inst();
        $api->initWebContext();
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;


        return A::cache()->get("Ekom.CategoryLayer.getCategoryIdTreeByProductId.$shopId.$langId.$productId", function () use ($api, $shopId, $langId, $productId) {

            $ret = [];
            $cardId = EkomApi::inst()->product()->readColumn("product_card_id", [
                ["id", "=", $productId],
            ]);
            if (false !== ($rows = $this->getCategoryTreeByProductCardId($cardId))) {
                foreach ($rows as $row) {
                    $ret[] = $row['id'];
                }
            }
            return $ret;
        }, [
            'ek_product',
            // getCategoryTreeByProductCardId
            'ek_category',
            'ek_category_lang',
        ]);
    }

    /**
     * This method return the id of all categories being contained inside a given category,
     * and including itself.
     */
    public function getDescendantCategoryIdTree($categoryId)
    {
        $categoryId = (int)$categoryId;


        return A::cache()->get("Ekom.CategoryLayer.getDescendantCategoryIdTree.$categoryId", function () use ($categoryId) {

            $ret = [$categoryId];
            // get descendants
            $this->doCollectDescendants($categoryId, $ret);
            sort($ret);
            return $ret;
        }, [
            'ek_category',
        ]);
    }


    /**
     * @param $slug
     * @return int|false
     */
    public function getIdBySlug($slug)
    {

        EkomApi::inst()->initWebContext();
        $shopId = (int)ApplicationRegistry::get("ekom.shop_id");
        $langId = (int)ApplicationRegistry::get("ekom.lang_id");

        $ret = A::cache()->get("Ekom.CategoryLayer.getIdBySlug.$shopId.$langId.$slug", function () use ($shopId, $langId, $slug) {


            if (false !== ($ret = QuickPdo::fetch("
select l.category_id
from ek_category_lang l
inner join ek_category c on c.id=l.category_id

where c.shop_id=$shopId 
and l.lang_id=$langId 
and l.slug=:slug

", [
                    "slug" => $slug,
                ], \PDO::FETCH_COLUMN))
            ) {
                return (int)$ret;
            }
            return $ret;
        }, [
            "ek_category_lang",
            "ek_category",
        ]);

        if (false === $ret) {
            XLog::error("[Ekom module] - CategoryLayer: no id found with slug $slug for shopId $shopId and langId $langId");
        }

        return $ret;

    }


    /**
     * This function can be used to create menu.
     *
     *
     *
     * @param $name
     * @param int $maxDepth =-1,
     *              - if -1, all the tree will be returned
     *              - if 0, only the current level (children of category identified by the given $name)
     *              - if any positive number, represents the maximum level that this function can reach
     *
     * @param string $wildCard , an extra identifier for modules; modules can use it as a marker with hooks.
     *
     *
     * @return array
     * Return an array of items, each of which having the following structure:
     *
     * - category_id
     * - label
     * - slug
     * - uri: a link to the category's page
     * - level: The current level of the node, starting at 0 and increasing
     * - children: array of children
     *
     *
     */

    public function getSubCategoriesByName($name, $maxDepth = -1, $wildCard = '')
    {
        EkomApi::inst()->initWebContext();
        $shopId = (int)ApplicationRegistry::get("ekom.shop_id");
        $langId = (int)ApplicationRegistry::get("ekom.lang_id");


        return A::cache()->get("Ekom.CategoryLayer.getSubCategoriesByName.$shopId.$langId.$name.$maxDepth.$wildCard", function () use ($shopId, $maxDepth, $name, $langId, $wildCard) {


            $rows = QuickPdo::fetchAll("
select 
cl.category_id,
cl.label,
cl.slug,
cl.description,
c.name

from ek_category c 
inner join ek_category_lang cl on cl.category_id=c.id 

where 
c.category_id = (
  select id from ek_category 
  where `name`=:cname
  and shop_id=$shopId
) 

and cl.lang_id=$langId

        
        
        ", [
                "cname" => $name,
            ]);

            $ret = [];
            $level = 0;

            $linkOptions = [
                'fn' => function (array $row) {
                    return E::link("Ekom_category", ['slug' => $row['slug']]);
                },
            ];
            Hooks::call("Ekom_categoryLayer_overrideLinkOptions", $linkOptions, $wildCard);

            foreach ($rows as $row) {

                $children = [];
                if (-1 === $maxDepth || $maxDepth > 0) {
                    $this->doCollectDescendantsInfo($row['category_id'], $children, $level + 1, $maxDepth);
                }
                $row['level'] = $level;
                $row['children'] = $children;
                $row['uri'] = call_user_func($linkOptions['fn'], $row);
                $ret[] = $row;
            }

            return $ret;

        }, [
            "ek_category",
            "ek_category_lang",
        ]);
    }

    public function getSubCategoriesBySlug($slug, $maxDepth = -1)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (int)ApplicationRegistry::get("ekom.shop_id");
        $langId = (int)ApplicationRegistry::get("ekom.lang_id");

        return A::cache()->get("Ekom.CategoryLayer.getSubCategoriesBySlug.$shopId.$langId.$slug.$maxDepth", function () use ($shopId, $maxDepth, $slug, $langId) {

            $name = QuickPdo::fetch("select 
c.name 
from ek_category c
inner join ek_category_lang cl on cl.category_id=c.id 
where 
c.shop_id=$shopId
and cl.lang_id=$langId 
and cl.slug=:slug
", [
                'slug' => $slug,
            ], \PDO::FETCH_COLUMN);


            return $this->getSubCategoriesByName($name, $maxDepth);
        }, [
            "ek_category",
            "ek_category_lang",
        ]);
    }

    /**
     * @return array of categories, or false
     */
    public function getCategoryTreeByProductCardId($cardId, $shopId = null, $langId = null) // might be promoted to public someday
    {
        $api = EkomApi::inst();
        $api->initWebContext();
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;

        /**
         * Get the category of the card for this shop
         */
        return A::cache()->get("Ekom.CategoryLayer.getCategoryTreeByProductCardId.$shopId.$langId.$cardId", function () use ($api, $shopId, $langId, $cardId) {
            $categoryId = $api->categoryHasProductCard()->readColumn("category_id", [
                ["product_card_id", "=", (int)$cardId],
            ]);
            if (false === $categoryId) {
                return false;
            }


            $treeRows = [];


            while (false !== ($parentRow = QuickPdo::fetch("select
c.id,
c.name,
c.category_id,
l.label,
l.slug
from ek_category c 
inner join ek_category_lang l on l.category_id=c.id
where c.id=$categoryId and c.category_id!=$categoryId and c.shop_id=$shopId and l.lang_id=$langId        
        "))) {
                $categoryId = $parentRow['category_id'];
                $treeRows[] = $parentRow;
                if (null === $parentRow['category_id']) {
                    break;
                }
            }
            return $treeRows;
        }, [
            'ek_category',
            'ek_category_lang',
        ]);
    }


    public function doCollectDescendants($categoryId, array &$ret, array &$leafIds = [])
    {
        $ids = QuickPdo::fetchAll("
select id from ek_category 
where category_id=$categoryId
and id != $categoryId
            ", [], \PDO::FETCH_COLUMN);

        if (0 === count($ids)) {
            $leafIds[] = $categoryId;
        }

        foreach ($ids as $id) {
            $ret[] = (int)$id;
            $this->doCollectDescendants($id, $ret, $leafIds);
        }
    }



    //--------------------------------------------
    //
    //--------------------------------------------



    private function doCollectDescendantsInfo($categoryId, array &$ret, $level = 0, $maxLevel = -1)
    {
        $rows = QuickPdo::fetchAll("
select
        
cl.category_id,
cl.label,
cl.slug,
c.name

from ek_category c 
inner join ek_category_lang cl on cl.category_id=c.id 
  
where c.category_id=$categoryId
and c.id != $categoryId
            ");
        foreach ($rows as $row) {

            $children = [];
            if (-1 === $maxLevel || $level < $maxLevel) {
                $this->doCollectDescendantsInfo($row['category_id'], $children, $level + 1, $maxLevel);
            }
            $row['uri'] = E::link("Ekom_category", ['slug' => $row['slug']]);
            $row['level'] = $level;
            $row['children'] = $children;
            $ret[] = $row;

        }
    }


    private function getTopCategoryIds($shopId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;

        /**
         * This is a function I use as a debug/dev tool, not used in the front,
         * hence I didn't create a cache, but feel free to...
         */
        return QuickPdo::fetchAll("
select id from ek_category
where category_id is null 
and shop_id=$shopId
        ", [], \PDO::FETCH_COLUMN);

    }

}