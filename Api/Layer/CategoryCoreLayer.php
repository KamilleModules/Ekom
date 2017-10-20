<?php


namespace Module\Ekom\Api\Layer;


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
 * Sources:
 * http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
 */
class CategoryCoreLayer
{
    private $_shop_id;
    private $_lang_id;
    private $_options;

    public function getSelfAndChildren($parentName, $maxLevel = -1, $shopId = null, $langId = false, array $options = [])
    {
        $ret = [];
        $this->_shop_id = $shopId;
        $this->_lang_id = $langId;
        $this->_options = $options;
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
            return $ret;
        }
        return [];
    }


    public function getLeafNodes()
    {
        throw new \Exception("Is that really useful?");
    }


    public function getSelfAndParents($catName, $maxLevel = -1)
    {
        $ret = [];
        $row = QuickPdo::fetch("
select * from ek_category where `name`=:name        
        ",
            ['name' => $catName]
        );
        if (false !== $row) {
            $ret[] = $row;
            if (0 !== $maxLevel) {
                $this->collectParentsByRow($row, $ret, $maxLevel);
            }
            return $ret;
        }
        return $ret;
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
        if (0 === $maxLevel--) {
            return;
        }
        $catId = $row['id'];
        $q = $this->getQuery("nested", $catId);

        $rows = QuickPdo::fetchAll("
select c.* from ek_category where category_id=$catId            
            ");


        foreach ($rows as $row) {
            $row['depth'] = $depth;
            $ret[] = $row;
            $this->collectChildrenByRow($row, $ret, $maxLevel, $depth + 1);
        }
    }


    private function collectParentsByRow(array $row, array &$ret, $maxLevel = 1, $depth = 1)
    {
        if (0 === $maxLevel--) {
            return;
        }
        $parentId = $row['category_id'];
        if (null === $parentId) {
            return;
        }
        $row = QuickPdo::fetch("
select * from ek_category where id=$parentId            
            ");
        if (false !== $row) {
            $ret[] = $row;
            $this->collectParentsByRow($row, $ret, $maxLevel);
        }
    }


    private function decorateQueryByShopIdLangId(&$query, $shopId, $langId)
    {
        if (false !== $shopId) {
            $shopId = E::getShopId($shopId);
            $query .= "
and c.shop_id=$shopId            
            ";
        }
        if (false !== $langId) {
            $langId = E::getShopId($langId);
            $query .= "
and c.lang_id=$langId            
            ";
        }
    }

    private function getQuery($type, $extra = null)
    {
        $options = array_replace([
            'order' => null,
        ], $this->_options);

        $shopId = $this->_shop_id;
        $langId = $this->_lang_id;

        $what = "c.*";
        if (false !== $langId) {
            $what .= ", cl.*";
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
        } else {
            $q .= "
where category_id=$extra        
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
and c.lang_id=$langId            
            ";
        }


        if (null !== $options['order']) {
            $q .= "
order by cl.label asc            
            ";
        }

        return $q;
    }

}