<?php


namespace Module\Ekom\Api\Layer;


use Bat\ArrayTool;
use Bat\CaseTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Object\CategoryHasProductCard;
use Module\Ekom\Models\EkomModels;
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


    public static function getCardIdsBoundToAnyCategory()
    {
        return QuickPdo::fetchAll("
select distinct(product_card_id) from ek_category_has_product_card
        ", [], \PDO::FETCH_COLUMN);
    }


    public static function getCategoryIdsByProductCardId(int $productCardId)
    {
        return QuickPdo::fetchAll("
select category_id from ek_category_has_product_card
where product_card_id=$productCardId        
        ", [], \PDO::FETCH_COLUMN);
    }


    public static function getItemsList(array $options = [])
    {
        $alphaSort = $options['alphaSort'] ?? false;
        $nameAsKey = $options['nameAsKey'] ?? false;
        $type = $options['type'] ?? null;
        $key = (true === $nameAsKey) ? "name" : "id";
        $q = "select $key, label from ek_category";
        $markers = [];
        if ($type) {
            $q .= " where type=:type";
            $markers['type'] = $type;
        }

        if ($alphaSort) {
            $q .= " order by label asc";
        }
        return QuickPdo::fetchAll($q, $markers, \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getTopCategoryInfoById(int $categoryId)
    {
        $catInfos = CategoryCoreLayer::create()->getSelfAndParentsByCategoryId($categoryId);
        array_pop($catInfos); // getting rid of the root category
        $topCatInfo = array_pop($catInfos);
        return $topCatInfo;
    }

    public static function getLabelBySlug(string $slug)
    {
        return QuickPdo::fetch("select label from ek_category where slug=:slug", ['slug' => $slug], \PDO::FETCH_COLUMN);
    }


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


    public static function getTree(array $options = [])
    {

        $withLinks = $options['withLinks'] ?? false;

        /**
         * @todo-ling: this guy really needs a cache
         */

        $linkFmt = A::link("Ekom_category", [
            "type" => '{type}',
            "slug" => '{slug}',
        ]);
        $o = new CategoryLayer();
        $ret = $o->getSubCategoriesByName("home", -1, "", true);
        if (true === $withLinks) {
            ArrayTool::updateNodeRecursive($ret, function (array &$row) use ($linkFmt) {
                $row['link'] = str_replace([
                    "{type}",
                    "{slug}",
                ], [
                    $row['type'],
                    $row['slug'],
                ], $linkFmt);
            });
        }
        return $ret;
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


    public static function getParentCategoryIdsById(int $categoryId)
    {
        $ret = [];
        $parentCats = CategoryCoreLayer::create()->getSelfAndParentsByCategoryId($categoryId);
        array_shift($parentCats); // only the parents, not self...
        foreach ($parentCats as $parentCat) {
            $ret[] = $parentCat['id'];
        }
        return $ret;
    }

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
c.category_id,
c.label,
c.type,
c.slug,
c.description,
c.name,
c.id

from ek_category c 
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


    public static function getCardIdsByCategoryName($categoryName, $recursive = true)
    {
        $categoryId = self::getCategoryIdByName($categoryName);
        return self::getCardIdsByCategoryId($categoryId, $recursive);
    }


    public static function getCardIdsByCategoryId($categoryId, $recursive = true)
    {
        return A::cache()->get("Ekom/CategoryLayer/getCardIdsByCategoryId-$categoryId-$recursive", function () use ($categoryId, $recursive) {

            $ret = [];
            if (true === $recursive) {
                $catInfos = CategoryCoreLayer::create()->getSelfAndChildrenByCategoryId($categoryId, -1);
                foreach ($catInfos as $info) {
                    $categoryId = $info['id'];
                    $cardIds = self::doGetCardIdsByCategoryId($categoryId);
                    $ret = array_merge($ret, $cardIds);
                }
            } else {
                $ret = self::doGetCardIdsByCategoryId($categoryId);
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

    public static function getSelfAndChildrenIdsById($categoryId)
    {
        $ids = [];
        $allCatItems = CategoryCoreLayer::create()->getSelfAndChildrenByCategoryId($categoryId, -1);
        foreach ($allCatItems as $item) {
            $ids[] = $item['id'];
        }
        return $ids;
    }


    /**
     * @param $slug
     * @return array|false
     *
     * @see EkomModels::categoryModel()
     *
     */
    public static function getInfoBySlug($slug)
    {
        return A::cache()->get("Ekom.CategoryLayer.getInfoBySlug.$slug", function () use ($slug) {
            return QuickPdo::fetch("
select
c.id, 
c.name, 
c.label,
c.slug
 
from ek_category c  
where c.slug=:slug         
        ", [
                'slug' => $slug,
            ]);
        }, "ek_category");

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


    public function collectCategoryInfoTreeByCategoryId(array &$infos, $categoryId)
    {
        $categoryId = (int)$categoryId;


        $row = A::cache()->get("Ekom.CategoryLayer.collectCategoryInfoTreeByCategoryId.$categoryId", function () use ($categoryId) {
            return QuickPdo::fetch("
select    

c.id,
c.type,
c.name,
c.category_id,
c.label,
c.description,
c.slug

from ek_category c 

where c.id=$categoryId
     
        ");
        }, "ek_category");

        if (false !== $row) {
            $infos[] = $row;
            if (null !== $row['category_id']) {
                $this->collectCategoryInfoTreeByCategoryId($infos, $row['category_id']);
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
    public function categoryIdBelongsTo($categoryId, $ancestorName)
    {
        $idAncestor = $this->getCategoryIdByName($ancestorName);
        $treeIds = [];
        $this->collectCategoryIdTreeByCategoryId($treeIds, $categoryId);
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


    public static function getCategoryIdByName($name)
    {
        return QuickPdo::fetch("select id from ek_category where name=:name", ['name' => $name], \PDO::FETCH_COLUMN);
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
    public function collectProductIdsByCategoryName(array &$ids, $categoryName, $maxNumber = 7)
    {
        $maxNumber = (int)$maxNumber;


        $catIds = [];
        $this->collectCategoryIdTreeByCategoryName($catIds, $categoryName);

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
select c.product_id from ek_product_card c 
inner join ek_category_has_product_card chpc on chpc.product_card_id=c.id
inner join ek_category c on c.id=chpc.category_id 

where c.id=$catId
and c.active=1 


            
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


    public function countProductReferencesByCategoryId(int $categoryId)
    {

        return A::cache()->get("Ekom.CategoryLayer.countProductReferencesByCategoryId.$categoryId", function () use ($categoryId) {
            $cardIds = self::getCardIdsByCategoryId($categoryId);
            if ($cardIds) {

                return QuickPdo::fetch("
select count(pr.id) as nb 
from ek_product_reference pr
inner join ek_product p on p.id=pr.product_id 
where p.product_card_id in (" . implode(", ", $cardIds) . ")        
        ", [], \PDO::FETCH_COLUMN);
            }
            return 0;
        });
    }


    /**
     * Return an array of the ids of the leaf categories (category without children).
     *
     *
     */
    public function getLeafCategoryIds($shopId = null)
    {

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
    public function collectCategoryIdTreeByCategoryId(array &$ids, $categoryId)
    {
        $ids[] = $categoryId;
        $parentCatId = EkomApi::inst()->category()->readColumn("category_id", [
            ["id", "=", $categoryId],
        ]);
        if (null !== $parentCatId) {
            $this->collectCategoryIdTreeByCategoryId($ids, $parentCatId);
        }
    }


    /**
     * This method return the id of the category and parent categories.
     */
    public function collectCategoryIdTreeByCategoryName(array &$ids, $categoryName)
    {
        $api = EkomApi::inst();

        return A::cache()->get("Ekom.CategoryLayer.collectCategoryIdTreeByCategoryName.$categoryName", function () use ($api, $categoryName, &$ids) {

            $id = EkomApi::inst()->category()->readColumn("id", [
                ["name", "=", $categoryName],
            ]);
            if (false !== $id) {
                $this->collectCategoryIdTreeByCategoryId($ids, $id);
            }
        }, [
            'ek_product',
            'ek_category',
        ]);
    }


    /**
     * This method return the id of the product's card categories and parent categories.
     */
    public function getCategoryIdTreeByProductId($productId)
    {
        return A::cache()->get("Ekom.CategoryLayer.getCategoryIdTreeByProductId.$productId", function () use ($productId) {

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
        });
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


        return A::cache()->get("Ekom.CategoryLayer.getSubCategoriesByName.$name.$maxDepth.$wildCard", function () use ($maxDepth, $name, $wildCard) {


            $rows = QuickPdo::fetchAll("
select 
id,
label,
type,
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
                    return E::link("Ekom_category", [
                        'slug' => $row['slug'],
                        'type' => $row['type'],
                    ]);
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

        }, [
            "ek_category",
        ]);
    }

    public function getSubCategoriesBySlug($slug, $maxDepth = -1)
    {

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
        return A::cache()->get("Ekom.CategoryLayer.getCategoryInfoById.$categoryId", function () use ($categoryId) {
            return QuickPdo::fetch("
select 
id,
`name`,
category_id,
label,
description,
slug,
meta_title,
meta_description,
meta_keywords


from ek_category  
where id=$categoryId
");
        }, "ek_category");
    }


    public function getSubCategoriesById($categoryId, $maxDepth = -1)
    {
        $categoryId = (int)$categoryId;

        return A::cache()->get("Ekom.CategoryLayer.getSubCategoriesById.$categoryId.$maxDepth", function () use ($maxDepth, $categoryId) {

            $name = QuickPdo::fetch("select 
name 
from ek_category 
where id=$categoryId
", [], \PDO::FETCH_COLUMN);


            return $this->getSubCategoriesByName($name, $maxDepth);
        }, [
            "ek_category",
        ]);
    }

    /**
     * @return array of categories, or false
     */
    public function getCategoryTreeByProductCardId($cardId) // might be promoted to public someday
    {
        $api = EkomApi::inst();

        /**
         * Get the category of the card for this shop
         */
        return A::cache()->get("Ekom.CategoryLayer.getCategoryTreeByProductCardId.$cardId", function () use ($api, $cardId) {
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
c.type,
c.category_id,
c.label,
c.slug
from ek_category c
where c.id=$categoryId and c.category_id!=$categoryId         
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

    private static function doGetCardIdsByCategoryId($categoryId)
    {
        $categoryId = (int)$categoryId;
        return QuickPdo::fetchAll("
select h.product_card_id
from ek_category c 
inner join ek_category_has_product_card h on h.category_id=c.id
and c.id=$categoryId
      ", [], \PDO::FETCH_COLUMN);
    }


    private function doCollectDescendantsInfo($categoryId, array &$ret, $level = 0, $maxLevel = -1)
    {
        $rows = QuickPdo::fetchAll("
select
     
id,
label,
type,
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
            $row['uri'] = E::link("Ekom_category", [
                'slug' => $row['slug'],
                'type' => $row['type'],
            ]);
            $row['level'] = $level;
            $row['children'] = $children;
            $ret[] = $row;

        }
    }


    private function getTopCategoryIds($shopId = null)
    {

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