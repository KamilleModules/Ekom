<?php


namespace Module\Ekom\Api\Layer;


use Bat\HashTool;
use Core\Services\A;
use Module\Ekom\Api\Util\HashUtil;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

/**
 * In this class,
 * I'm trying to use the parent/children keywords
 *
 * shop_id and lang_id
 * ======================
 * The values can be one of:
 * - false, will not be used
 * - null, the current shop_id/lang_id will be used if any
 * - int, your value
 *
 * The default shop_id is null.
 * The default lang_id is false.
 * If lang_id is false,
 *      the return data set will contain the following data:
 *          - id
 *          - name
 *          - category_id
 *          - shop_id
 *          - order
 *          - depth
 *
 * If land_id is not false, then the returned data will have the following structure:
 *          - id
 *          - name
 *          - category_id
 *          - shop_id
 *          - order
 *          - depth
 *          - lang_id
 *          - label
 *          - description
 *          - slug
 *          - meta_title
 *          - meta_description
 *          - meta_keywords
 *
 *
 *
 *
 *
 * inspirational sources:
 * http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
 *
 * Note: I didn't implement nested model because:
 * - it's easier to make hot-update (using phpmyadmin) with the adjacency model
 * - the approach of the adjacency model is more intuitive to me
 * - if my boss ask me a complex request, I will feel more comfortable with the adjacency model
 *
 */
class CategoryCoreLayer
{
    private $_shop_id;
    private $_lang_id;
    private static $cpt = 0;
    private static $max = 40;

    /**
     * @param $parentName
     * @param int $maxLevel
     * @param null $shopId
     * @param bool $langId
     * @param array $options
     *          - order: [$field, asc|desc]
     * @return array
     */
    public function getSelfAndChildren($parentName, $maxLevel = -1, $shopId = null, $langId = false, array $options = [])
    {

        $hash = HashTool::getHashByArray($options);
        $mix = "$parentName.$maxLevel.$shopId.$langId.$hash";
        self::$cpt = 0;

        return A::cache()->get("Ekom.CategoryCoreLayer.getSelfAndChildren.$mix", function () use ($shopId, $langId, $parentName, $maxLevel, $options) {
            $ret = [];
            $this->_shop_id = $shopId;
            $this->_lang_id = $langId;
            $q = $this->getQuery("name");
            $row = QuickPdo::fetch($q,
                ['name' => $parentName]
            );

            if (false !== $row) {
                $depth = 0;
                $row['depth'] = $depth;
                $ret[] = $row;
                if (0 !== $maxLevel) {
                    $this->collectChildrenByRow($row, $ret, $maxLevel, $depth + 1);
                }
                $this->applySort($ret, $options);
            }


            return $ret;
        }, [
            'ek_category',
            'ek_category_lang',
        ]);
    }


    public function getLeafNodes()
    {
        throw new \Exception("Is that really useful?");
    }


    public function getSelfAndParents($catName, $maxLevel = -1, $shopId = null, $langId = false, $options = [])
    {

        $hash = HashTool::getHashByArray($options);
        $mix = "$catName.$maxLevel.$shopId.$langId.$hash";
        self::$cpt = 0;

        return A::cache()->get("Ekom.CategoryCoreLayer.getSelfAndParents.$mix", function () use ($shopId, $langId, $catName, $maxLevel, $options) {
            $ret = [];
            $this->_shop_id = $shopId;
            $this->_lang_id = $langId;


            $q = $this->getQuery("name");
            $row = QuickPdo::fetch($q,
                ['name' => $catName]
            );


            if (false !== $row) {
                $ret[] = $row;
                if (0 !== $maxLevel) {
                    $this->collectParentsByRow($row, $ret, $maxLevel);
                }
                $this->applySort($ret, $options);
            }
            return $ret;
        }, [
            'ek_category',
            'ek_category_lang',
        ]);


    }

    public function getSelfAndParentsByCategoryId($catId, $maxLevel = -1, $shopId = null, $langId = false, $options = [])
    {

        $hash = HashTool::getHashByArray($options);
        $mix = "$catId.$maxLevel.$shopId.$langId.$hash";
        self::$cpt = 0;

        return A::cache()->get("Ekom.CategoryCoreLayer.getSelfAndParentsByCategoryId.$mix", function () use ($shopId, $langId, $catId, $maxLevel, $options) {
            $ret = [];
            $this->_shop_id = $shopId;
            $this->_lang_id = $langId;


            $q = $this->getQuery("id", $catId);
            $row = QuickPdo::fetch($q);


            if (false !== $row) {
                $ret[] = $row;
                if (0 !== $maxLevel) {
                    $this->collectParentsByRow($row, $ret, $maxLevel);
                }
                $this->applySort($ret, $options);
            }
            return $ret;
        }, [
            'ek_category',
            'ek_category_lang',
        ]);


    }


    /**
     * If $catName is provided, the subtree depth starts at the $catName level (i.e. $catName.level=0)
     */
    public function getName2Depth($catName = null)
    {
        $ret = [];
        if (null === $catName) {
            $catName = QuickPdo::fetch("select name from ek_category where category_id is null", [], \PDO::FETCH_COLUMN);
            if (false === $catName) {
                return [];
            }
        }

        $cats = $this->getSelfAndChildren($catName);
        foreach ($cats as $cat) {
            $ret[$cat["name"]] = $cat['depth'];
        }
        return $ret;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function collectChildrenByRow(array $row, array &$ret, $maxLevel = 1, $depth = 1)
    {
        if (self::$cpt++ > self::$max) {
            throw new \Exception("infinite loop prevention for collectChildrenByRow");
        }
        if (0 === $maxLevel--) {
            return;
        }
        $catId = $row['id'];
        $q = $this->getQuery("collectChildrenByRow", $catId);
        $rows = QuickPdo::fetchAll($q);


        foreach ($rows as $row) {
            $row['depth'] = $depth;
            $ret[] = $row;
            $this->collectChildrenByRow($row, $ret, $maxLevel, $depth + 1);
        }
    }


    private function collectParentsByRow(array $row, array &$ret, $maxLevel = 1)
    {
        if (self::$cpt++ > self::$max) {
            throw new \Exception("infinite loop prevention for collectParentsByRow");
        }
        if (0 === $maxLevel--) {
            return;
        }
        $parentId = $row['category_id'];
        if (null === $parentId) {
            return;
        }

        $q = $this->getQuery("collectParentsByRow", $parentId);
        $row = QuickPdo::fetch($q);

        if (false !== $row) {
            $ret[] = $row;
            $this->collectParentsByRow($row, $ret, $maxLevel);
        }
    }


    private function getQuery($type, $extra = null)
    {

        $shopId = $this->_shop_id;
        $langId = $this->_lang_id;

        $what = "c.*";
        if (false !== $langId) {
            $what .= ",
cl.lang_id,            
cl.label,            
cl.description,            
cl.slug,            
cl.meta_title,            
cl.meta_description,            
cl.meta_keywords            
";
        }
        $q = "
select $what 
from ek_category c         
        ";

        if (false !== $langId) {
            $q .= "
inner join ek_category_lang cl on cl.category_id=c.id
            ";
        }


        if ('name' === $type) {

            $q .= "
where c.name=:name        
        ";
        } elseif ('id' === $type) {

            $q .= "
where c.id=$extra        
        ";
        } elseif ('collectChildrenByRow' === $type) {
            $q .= "
where c.category_id=$extra        
        ";
        } elseif ('collectParentsByRow' === $type) {

            $q .= "
where c.id=$extra        
        ";
        }


        if (false !== $shopId) {
            $shopId = E::getShopId($shopId);
            $q .= "
and c.shop_id=$shopId            
            ";
        }
        if (false !== $langId) {
            $langId = E::getShopId($langId);
            $q .= "
and cl.lang_id=$langId            
            ";
        }

        return $q;
    }


    private function applySort(array &$ret, array $options)
    {
        if (array_key_exists('order', $options)) {
            $order = $options['order'];
            list($field, $direction) = $order;
            if ('asc' === $direction) {
                usort($ret, function ($a, $b) use ($field, $direction) {
                    return $a[$field] > $b[$field];
                });
            } else {
                usort($ret, function ($a, $b) use ($field, $direction) {
                    return $a[$field] < $b[$field];
                });
            }
        }
    }
}