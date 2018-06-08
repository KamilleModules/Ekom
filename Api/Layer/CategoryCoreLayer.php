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
 * There are two types of items that this class returns:
 *
 * the basic item
 * -----------------
 *      the return data set will contain the following data:
 *          - id
 *          - name
 *          - category_id
 *          - shop_id
 *          - order
 *          - depth
 *
 * the extended item
 * -----------------
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
    private $_extended;
    private static $cpt = 0;
    private static $max = 1000;


    public static function create()
    {
        return new static();
    }

    /**
     * @param $parentName
     * @param int $maxLevel
     * @param null $shopId
     * @param bool $langId
     * @param array $options
     *          - order: [$field, asc|desc]
     * @return array
     */
    public function getSelfAndChildren($parentName, $maxLevel = -1, array $options = [])
    {
        $this->_extended = (array_key_exists('extended', $options)) ? (bool)$options['extended'] : false;
        $hash = HashTool::getHashByArray($options);
        $mix = "$parentName.$maxLevel--$hash";
        self::$cpt = 0;

        return A::cache()->get("Ekom/CategoryCoreLayer/getSelfAndChildren-$mix", function () use ($parentName, $maxLevel, $options) {
            $ret = [];
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
        });
    }


    /**
     * @param $parentId
     * @param int $maxLevel
     * @param array $options
     *          - order: [$field, asc|desc]
     * @return array
     */
    public function getSelfAndChildrenByCategoryId($parentId, $maxLevel = -1, array $options = [], $forceGenerate = false)
    {
        $this->_extended = (array_key_exists('extended', $options)) ? (bool)$options['extended'] : false;
        $hash = HashTool::getHashByArray($options);
        $mix = "$parentId.$maxLevel--$hash";
        self::$cpt = 0;

        return A::cache()->get("Ekom/CategoryCoreLayer/getSelfAndChildrenByCategoryId-$mix", function () use ($parentId, $maxLevel, $options) {
            $ret = [];
            $q = $this->getQuery("id", $parentId);
            $row = QuickPdo::fetch($q);

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
        }, $forceGenerate);
    }


    public function getLeafNodes()
    {
        throw new \Exception("Is that really useful?");
    }


    public function getSelfAndParents($catName, $maxLevel = -1, $options = [])
    {
        $this->_extended = (array_key_exists('extended', $options)) ? (bool)$options['extended'] : false;
        $hash = HashTool::getHashByArray($options);
        $mix = "$catName.$maxLevel--$hash";
        self::$cpt = 0;

        return A::cache()->get("Ekom/CategoryCoreLayer/getSelfAndParents-$mix", function () use ($catName, $maxLevel, $options) {
            $ret = [];


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
        });


    }

    public function getSelfAndParentsByCategoryId($catId, $maxLevel = -1, $options = [])
    {
        $this->_extended = (array_key_exists('extended', $options)) ? (bool)$options['extended'] : false;
        $hash = HashTool::getHashByArray($options);
        $mix = "$catId.$maxLevel--$hash";
        self::$cpt = 0;

        return A::cache()->get("Ekom/CategoryCoreLayer/getSelfAndParentsByCategoryId-$mix", function () use ($catId, $maxLevel, $options) {
            $ret = [];


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
            "ek_category",
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

        $what = "*";
        if (true === $this->_extended) {
            $what .= ",            
label,            
description,            
slug,            
meta_title,            
meta_description,            
meta_keywords            
";
        }
        $q = "
select $what 
from ek_category          
        ";



        if ('name' === $type) {

            $q .= "
where name=:name        
        ";
        } elseif ('id' === $type) {

            $q .= "
where id=$extra        
        ";
        } elseif ('collectChildrenByRow' === $type) {
            $q .= "
where category_id=$extra        
        ";
        } elseif ('collectParentsByRow' === $type) {

            $q .= "
where id=$extra        
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