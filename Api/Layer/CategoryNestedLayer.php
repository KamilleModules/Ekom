<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

/**
 * Trying to use the parent/children keywords
 *
 *
 * Sources:
 * http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
 */
class CategoryNestedLayer
{


    public function getSelfAndChildren($parentName)
    {
        return QuickPdo::fetchAll("
SELECT node.name
FROM nested_category AS node,
        nested_category AS parent
WHERE node.lft BETWEEN parent.lft AND parent.rgt
        AND parent.name = :parent
ORDER BY node.lft;        
        ", [
            'parent' => $parentName,
        ]);
    }


    public function getLeafNodes()
    {
        return QuickPdo::fetchAll("
SELECT name
FROM nested_category
WHERE rgt = lft + 1;        
        ");
    }


    public function getSelfAndParents($catName)
    {
        return QuickPdo::fetchAll("
SELECT parent.name
FROM nested_category AS node,
        nested_category AS parent
WHERE node.lft BETWEEN parent.lft AND parent.rgt
        AND node.name = :name
ORDER BY parent.lft;
        ", [
            'name' => $catName,
        ]);
    }


    /**
     * If $catName is provided, the subtree depth starts at the $catName level (i.e. $catName.level=0)
     */
    public function getName2Depth($catName = null)
    {
        if (null === $catName) {

            return QuickPdo::fetchAll("
SELECT node.name, (COUNT(parent.name) - 1) AS depth
FROM nested_category AS node,
        nested_category AS parent
WHERE node.lft BETWEEN parent.lft AND parent.rgt
GROUP BY node.name
ORDER BY node.lft;
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        } else {
            return QuickPdo::fetchAll("
SELECT node.name, (COUNT(parent.name) - (sub_tree.depth + 1)) AS depth
FROM nested_category AS node,
        nested_category AS parent,
        nested_category AS sub_parent,
        (
                SELECT node.name, (COUNT(parent.name) - 1) AS depth
                FROM nested_category AS node,
                nested_category AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                AND node.name = :catname
                GROUP BY node.name
                ORDER BY node.lft
        )AS sub_tree
WHERE node.lft BETWEEN parent.lft AND parent.rgt
        AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
        AND sub_parent.name = sub_tree.name
GROUP BY node.name
ORDER BY node.lft;
        ", [
                'catname' => $catName,
            ], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        }
    }


}