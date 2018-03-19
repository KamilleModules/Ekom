<?php


namespace Module\Ekom\Api\Layer;


use Bat\CaseTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Object\CategoryHasProductCard;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 * CategoryInfo
 * =================
 * (data from ek_category)
 * - id: the category id
 * - name: the category name
 *
 */
class CategoryLayer
{


    public static function reorderCategories()
    {


        $o = new CategoryLayer();
        $cats = $o->getSubCategoriesByName("home", 0, "", true);
        self::reorderChildren($cats);

        CategoryLayer::visitTree(function ($node) {
            $children = $node['children'];
            if ($children) {
                self::reorderChildren($children);
            }
        });

    }


    public static function getTree()
    {
        $o = new CategoryLayer();
        return $o->getSubCategoriesByName("home", -1, "", true);
    }

    public static function visitTree(callable $cb)
    {
        $tree = self::getTree();
        foreach ($tree as $node) {
            self::visitNode($node, $cb);
        }
    }


    public static function associateProductCardWithCategories($cardId, array $categoryIds)
    {
        // first clean the association of this card to categories
        $cardId = (int)$cardId;
        QuickPdo::delete("ek_category_has_product_card", [
            ['product_card_id', "=", $cardId],
        ]);


        foreach ($categoryIds as $id) {
            $id = (int)$id;
            $o = new CategoryHasProductCard();
            $o->create([
                'product_card_id' => $cardId,
                'category_id' => $id,
            ]);
        }
    }


    public static function deleteCategory($categoryId)
    {
        $whereConds = [
            ["id", "=", $categoryId],
        ];
        QuickPdo::delete("ek_category", $whereConds);
    }

    public static function getLastChildrenInfo($categoryId)
    {
        $categoryId = (int)$categoryId;
        return QuickPdo::fetch("
select * from ek_category 
where category_id=$categoryId
order by `order` desc         
        ");
    }

    /**
     * This method is used along with a gui tool.
     * It should be used at the end of a drag'n'drop.
     *
     */
    public static function moveCategory($sourceId, $targetId, $mode, &$error = null)
    {
        switch ($mode) {
            case "before":
            case "after":
                $parentId = CategoryLayer::getParentCategoryIdById($targetId);


                $childrenItems = CategoryCoreLayer::create()->getSelfAndChildrenByCategoryId($parentId, 1, [
                    'order' => ["order", "asc"],
                ], true);
                array_shift($childrenItems);
                $childrenId2Order = [];
                $targetOrder = 0;
                foreach ($childrenItems as $item) {
                    $childrenId2Order[$item['id']] = $item['order'];
                    if ((int)$item['id'] === (int)$targetId) {
                        $targetOrder = $item['order'];
                    }
                }

                $newTargetOrder = $targetOrder;

                if ('after' === $mode) {
                    $newTargetOrder += 1;
                }


                $q = "
update ek_category set `order` = `order`+1
where category_id=$parentId
and `order` >= $newTargetOrder
    ";

                QuickPdo::freeQuery($q);
                QuickPdo::update("ek_category", [
                    'category_id' => $parentId,
                    'order' => $newTargetOrder,
                ], [
                    ["id", "=", $sourceId],
                ]);

                return true;
                break;
            case "over":
                $lastChildren = CategoryLayer::getLastChildrenInfo($targetId);
                if (false === $lastChildren) { // this node doesn't have a children yet
                    $newOrder = 0;
                } else {
                    $newOrder = $lastChildren['order'] + 1;
                }

                QuickPdo::update("ek_category", [
                    "order" => $newOrder,
                    "category_id" => $targetId,
                ], [
                    ["id", "=", $sourceId],
                ]);
                return true;
                break;
            default:
                $error = "Unknown mode $mode";
                return false;
                break;
        }
        return false;
    }

//    public static function getChildrenIds($catId, $shopId, $langId, $forceGenerate = false)
//    {
//        $childrenItems = CategoryCoreLayer::create()->getSelfAndChildrenByCategoryId($catId, 1, $shopId, $langId, [
//            'order' => ["order", "asc"],
//        ], $forceGenerate);
//        array_shift($childrenItems);
//        $childrenIds = [];
//        foreach ($childrenItems as $item) {
//            $childrenIds[] = $item['id'];
//        }
//        return $childrenIds;
//    }
//
//    public static function getChildrenInfo($catId, $shopId, $langId, $forceGenerate = false)
//    {
//        $childrenItems = CategoryCoreLayer::create()->getSelfAndChildrenByCategoryId($catId, 1, $shopId, $langId, [
//            'order' => ["order", "asc"],
//        ], $forceGenerate);
//        array_shift($childrenItems);
//        $childrenIds = [];
//        foreach ($childrenItems as $item) {
//            $childrenIds[] = $item['id'];
//        }
//        return $childrenIds;
//    }


    public static function getParentCategoryIdById($catId)
    {
        $catId = (int)$catId;
        return QuickPdo::fetch("
select category_id from ek_category where id=$catId        
        ", [], \PDO::FETCH_COLUMN);
    }


    public static function checkSlugUnique($categoryId, $langId, $slug)
    {
        $categoryId = (int)$categoryId;
        $langId = (int)$langId;
        $row = QuickPdo::fetch("
select category_id from ek_category_lang
where category_id=$categoryId
and lang_id=$langId
and slug = :slug        
        ", [
            'slug' => $slug,
        ]);
        if (false === $row) {
            return true;
        }
        return false;
    }


    public static function getCategoryItemsByShopId($shopId)
    {
        $shopId = (int)$shopId;
        return QuickPdo::fetchAll("
select id, name from ek_category where shop_id=$shopId        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function countCategoriesByShopId($shopId)
    {
        $shopId = (int)$shopId;
        return QuickPdo::fetch("
select count(*) as count 
from ek_category 
where shop_id=$shopId        
        ", [], \PDO::FETCH_COLUMN);
    }

    public static function getNameById($categoryId)
    {
        $categoryId = (int)$categoryId;
        return QuickPdo::fetch("
select `name` from ek_category where id=$categoryId        
        ", [], \PDO::FETCH_COLUMN);
    }

    public static function getCategoryGridItemByNames(array $names)
    {
        if ($names) {
            $ret = QuickPdo::fetchAll("
select        
l.category_id,
l.label,
l.slug,
l.description,
c.name,
c.id

from ek_category c 
inner join ek_category_lang l on l.category_id=c.id
where c.name in ('" . implode("','", $names) . "')

        ");
            foreach ($ret as $k => $info) {
                $nbCats = count(CategoryCoreLayer::create()->getSelfAndChildren($info['name']));
                $info['nbCats'] = $nbCats;
                $ret[$k] = $info;
            }
            return $ret;
        } else {
            return [];
        }
    }


    public static function getCardIdsByCategoryName($categoryName, $shopId = null, $recursive = true)
    {
        $categoryId = self::getCategoryIdByName($categoryName, $shopId);
        return self::getCardIdsByCategoryId($categoryId, $shopId, $recursive);
    }

    public static function getCardIdsByCategoryId($categoryId, $shopId = null, $recursive = true)
    {
        $shopId = E::getShopId($shopId);
        return A::cache()->get("Ekom/CategoryLayer/getCardIdsByCategoryId-$categoryId-$shopId-$recursive", function () use ($categoryId, $shopId, $recursive) {

            $ret = [];
            if (true === $recursive) {
                $catInfos = CategoryCoreLayer::create()->getSelfAndChildrenByCategoryId($categoryId, -1, $shopId);
                foreach ($catInfos as $info) {
                    $categoryId = $info['id'];
                    $cardIds = self::doGetCardIdsByCategoryId($categoryId, $shopId);
                    $ret = array_merge($ret, $cardIds);
                }
            } else {
                $ret = self::doGetCardIdsByCategoryId($categoryId, $shopId);
            }
            $ret = array_unique($ret);
            sort($ret);
            return $ret;
        });
    }


    /**
     * Collect product card ids contained in the given category and children.
     */
    public function collectProductCardIdsDescendantsByCategoryName(array &$ids, $categoryName, $shopId = null)
    {


        $catId = $this->getCategoryIdByName($categoryName, $shopId);
        $catIds = [];
        $leafIds = [];
        $this->doCollectDescendants($catId, $catIds, $leafIds);
        $ids = EkomApi::inst()->productCardLayer()->getProductCardIdsByCategoryIds($catIds);
    }

    public static function getIdByName($name, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        return QuickPdo::fetch("
select id 
from ek_category 
where shop_id=$shopId
and `name`=:name
        ", [
            "name" => $name,
        ], \PDO::FETCH_COLUMN);
    }

    public static function getSelfAndChildrenIdsById($categoryId, $shopId = null, $langId = null)
    {
        $ids = [];
        $allCatItems = CategoryCoreLayer::create()->getSelfAndChildrenByCategoryId($categoryId, -1, $shopId, $langId);
        foreach ($allCatItems as $item) {
            $ids[] = $item['id'];
        }
        return $ids;
    }

    public static function getInfoBySlug($slug, $shopId = null, $langId = null)
    {
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);

        return QuickPdo::fetch("
select
c.id, 
c.name, 
cl.label, 
c.shop_id
 
from ek_category c 
inner join ek_category_lang cl on cl.category_id=c.id 
where cl.slug=:slug 
and cl.lang_id=$langId 
and c.shop_id=$shopId        
        ", [
            'slug' => $slug,
        ]);
    }


    public function getUpCategoryInfosById($categoryId, $topToBottom = true, $shopId = null, $langId = null)
    {
        $infos = [];
        $this->collectCategoryInfoTreeByCategoryId($infos, $categoryId, $shopId, $langId);
        if (true === $topToBottom) {
            $infos = array_reverse($infos);
        }
        return $infos;
    }


    public function collectCategoryInfoTreeByCategoryId(array &$infos, $categoryId, $shopId = null, $langId = null)
    {
        $categoryId = (int)$categoryId;
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);


        $row = QuickPdo::fetch("
select    

c.id,
c.name,
c.category_id,
cl.label,
cl.description,
cl.slug

from ek_category c 
inner join ek_category_lang cl on cl.category_id=c.id

where c.id=$categoryId
and c.shop_id=$shopId
and cl.lang_id=$langId


     
        ");

        if (false !== $row) {
            $infos[] = $row;
            if (null !== $row['category_id']) {
                $this->collectCategoryInfoTreeByCategoryId($infos, $row['category_id'], $shopId, $langId);
            }

        }
    }


    public function getCategoryInfoByCardIds(array $cardIds, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        $sIds = implode(', ', array_map('intval', $cardIds));

        return QuickPdo::fetchAll("
select 
c.id,
c.name,
c.category_id,
h.product_card_id

from ek_category c 
inner join ek_category_has_product_card h on h.category_id=c.id


where c.shop_id=$shopId 
and h.product_card_id in ($sIds)


        ");


    }


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


    public static function getCategoryIdByName($name, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        return QuickPdo::fetch("select id from ek_category where name=:name and shop_id=$shopId", ['name' => $name], \PDO::FETCH_COLUMN);
    }


    public function getCategoryNameById($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("select `name` from ek_category where id=$id", [], \PDO::FETCH_COLUMN);
    }

    public function getCategoryNameBySlug($slug, $langId = null)
    {
        $langId = E::getLangId($langId);
        return QuickPdo::fetch("
select c.`name` 
from ek_category c
inner join ek_category_lang cl on cl.category_id=c.id 
where
cl.slug=:slug
and cl.lang_id=$langId

", [
            'slug' => $slug,
        ], \PDO::FETCH_COLUMN);
    }


    /**
     * Collect product card ids contained in the given category and children.
     */
    public function collectProductCardIdsDescendantsByCategoryIds(array &$ids, array $categoryIds)
    {

        $allCatIds = [];
        $leafIds = [];
        foreach ($categoryIds as $id) {
            $catIds = [];
            $this->doCollectDescendants($id, $catIds, $leafIds);
            $allCatIds = array_merge($allCatIds, $catIds);
        }
        $allCatIds = array_unique($allCatIds);
        $ids = EkomApi::inst()->productCardLayer()->getProductCardIdsByCategoryIds($allCatIds);
    }

    /**
     * Collect product for product contained in the given category and children.
     */
    public function collectProductCardInfoDescendantsByCategoryName(array &$infos, $categoryName, $shopId = null)
    {


        $catId = $this->getCategoryIdByName($categoryName, $shopId);
        $catIds = [];
        $leafIds = [];
        $this->doCollectDescendants($catId, $catIds, $leafIds);
        $infos = EkomApi::inst()->productCardLayer()->getProductCardInfosByCategoryIds($catIds);
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
        if (null === $shopId) {
            $shopId = E::getShopId();
        }


        $ids[] = $categoryId;
        $parentCatId = EkomApi::inst()->category()->readColumn("category_id", [
            ["id", "=", $categoryId],
        ]);
        if (null !== $parentCatId) {
            $this->collectCategoryIdTreeByCategoryId($ids, $parentCatId, $shopId);
        }
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

        return A::cache()->get("Ekom.CategoryLayer.collectCategoryIdTreeByCategoryName.$shopId.$categoryName", function () use ($api, $shopId, $categoryName, &$ids) {

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


    public function getSubCategoriesInfoByName($name, $includeSelfInChildren = true, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        $ret = [];
        $topCats = $this->getSubCategoriesByName($name, -1, '', $shopId);
        foreach ($topCats as $info) {

            $childrenIds = [];
            $this->collectChildrenIds($info['children'], $childrenIds);
            if (true === $includeSelfInChildren) {
                $childrenIds[] = $info['category_id'];
            }
            $info['children'] = $childrenIds;

            $ret[] = $info;
        }
        return $ret;
    }


    private function collectChildrenIds(array $children, array &$childrenIds)
    {
        foreach ($children as $child) {
            $childrenIds[] = $child['category_id'];
            if ($child['children']) {
                $this->collectChildrenIds($child['children'], $childrenIds);
            }
        }
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
    public function getSubCategoriesByName($name, $maxDepth = -1, $wildCard = '', $forceGenerate = false)
    {
        EkomApi::inst()->initWebContext();


        return A::cache()->get("Ekom.CategoryLayer.getSubCategoriesByName.$name.$maxDepth.$wildCard", function () use ($maxDepth, $name, $wildCard) {


            $rows = QuickPdo::fetchAll("
select 
id,
label,
slug,
description,
`name`,
`order`

from ek_category  

where 
category_id = (
  select id from ek_category 
  where `name`=:cname
) 

order by `order` asc
        
        
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
                    $this->doCollectDescendantsInfo($row['id'], $children, $level + 1, $maxDepth);
                }
                $row['level'] = $level;
                $row['children'] = $children;
                $row['uri'] = call_user_func($linkOptions['fn'], $row);
                $ret[] = $row;
            }

            return $ret;

        }, $forceGenerate);
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


    public function getCategoryInfoById($categoryId)
    {
        $categoryId = (int)$categoryId;
        return QuickPdo::fetch("
select 
c.id,
c.name,
c.category_id,
c.shop_id,
cl.label,
cl.description,
cl.slug,
cl.meta_title,
cl.meta_description,
cl.meta_keywords


from ek_category c 
inner join ek_category_lang cl on cl.category_id=c.id
where c.id=$categoryId
");
    }


    public function getSubCategoriesById($categoryId, $maxDepth = -1, $shopId = null, $langId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);
        $categoryId = (int)$categoryId;

        return A::cache()->get("Ekom.CategoryLayer.getSubCategoriesById.$shopId.$langId.$categoryId.$maxDepth", function () use ($shopId, $maxDepth, $categoryId, $langId) {

            $name = QuickPdo::fetch("select 
name 
from ek_category 
where 
shop_id=$shopId 
and id=$categoryId
", [], \PDO::FETCH_COLUMN);


            return $this->getSubCategoriesByName($name, $maxDepth);
        }, [
            "ek_category",
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

    private static function doGetCardIdsByCategoryId($categoryId, $shopId = null)
    {
        $categoryId = (int)$categoryId;
        $shopId = E::getShopId($shopId);
        return QuickPdo::fetchAll("
select h.product_card_id
from ek_category c 
inner join ek_category_has_product_card h on h.category_id=c.id
where c.shop_id=$shopId      
and c.id=$categoryId
      ", [], \PDO::FETCH_COLUMN);
    }


    private function doCollectDescendantsInfo($categoryId, array &$ret, $level = 0, $maxLevel = -1)
    {
        $rows = QuickPdo::fetchAll("
select
     
id,
label,
slug,
`name`,
`order`

from ek_category  
  
where category_id=$categoryId
and id != $categoryId

order by `order` asc


            ");
        foreach ($rows as $row) {

            $children = [];
            if (-1 === $maxLevel || $level < $maxLevel) {
                $this->doCollectDescendantsInfo($row['id'], $children, $level + 1, $maxLevel);
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


    private static function visitNode(array $node, callable $cb)
    {
        $cb($node);
        if (array_key_exists("children", $node) && $node['children']) {
            foreach ($node['children'] as $childNode) {
                self::visitNode($childNode, $cb);
            }
        }
    }


    private static function reorderChildren(array $children)
    {
        $previous = null;
        foreach ($children as $node) {
            $order = (int)$node['order'];
            if (null === $previous) {
                $previous = $order;
            } else {
                if ($order > $previous) {
                    $previous = $order;
                } elseif ($order <= $previous) {
                    $previous++;
                    QuickPdo::update("ek_category", [
                        "order" => $previous,
                    ], [
                        ["id", "=", $node['id']],
                    ]);
                }
            }
        }
    }
}