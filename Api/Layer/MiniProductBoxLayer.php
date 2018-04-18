<?php


namespace Module\Ekom\Api\Layer;

use Core\Services\A;
use Core\Services\Hooks;
use Module\Ekom\Api\Util\ProductQueryBuilderUtil;
use QuickPdo\QuickPdo;


/**
 * 2018-04-13
 *
 * Caching is a real problem.
 * Having a fast implementation upfront is a good thing I believe.
 *
 * In Ekom, there was first the concept of productBox: an array containing ALL (and I really mean all)
 * info that you would ever want to display a product.
 *
 * ProductBox was nice, because you could display ANYTHING.
 *
 * Now, it's my second pass on this application, and I believe it's just too heavy compared to what data we really need.
 * Especially with lists.
 * Sure, when you display a product PAGE, THEN OK you need the ProductBox,
 * but for list items, it's a little overkilled, since list items display only a small subset of the data.
 *
 * Actually, one of my goal recently has been to revisit the database structure with the goal of flattening data,
 * (my work is related to discounts and taxes), so that we could query a product using the simple sql engine (mysql).
 *
 * Now the idea with MiniProductBox promotes this flattened db design, and basically is the proof that this flattened
 * technique actually is useful on the front.
 *
 * MiniProductBox will give you the main info that you will want on a list of products, like a real list, or a carousel
 * for instance.
 *
 * I didn't include the attributes yet, because first of all attributes are not flat by default (i.e. if you want attributes,
 * you need a subquery), and in my case having attributes on list items was a topic on the table,
 * but has been dropped for some reasons.
 *
 * I agree that attributes on a list item is something that one should be able to do easily.
 * For that, I will just say that in order to keep the flattening system intact (and thus having a light/fast app),
 * one should implement attributes by flattening them (maybe in a ek_product_flat_attributes table).
 *
 *
 * Ok, enough talking, let's code...
 *
 *
 *
 *
 * We will provide the following miniBoxModel for templates:
 *
 * @see EkomModels::miniBoxModel()
 *
 *
 */
class MiniProductBoxLayer
{


    public static function getBoxesByProductGroupName(string $productGroupName)
    {


            $sqlQuery = ProductQueryBuilderUtil::getBaseQuery();

            // specific to groups
            $sqlQuery->addWhere("
and g.name = :group_name        
        ");
            $sqlQuery->addJoin("
inner join ek_product_group_has_product phg on phg.product_id=p.id
inner join ek_product_group g on g.id=phg.product_group_id
            ");
            $sqlQuery->addMarker("group_name", $productGroupName);
            $sqlQuery->addOrderBy("phg.order", "asc");



            $rows = QuickPdo::fetchAll((string)$sqlQuery, $sqlQuery->getMarkers());
            self::sugarifyRows($rows);
            return $rows;
    }


    /**
     * ek_product_group
     * @param $cardId , int
     * @return array
     */
    public static function getRelatedProductBoxListByCardId(int $cardId)
    {
        return self::getBoxesByProductGroupName(":related-$cardId");
    }


    /**
     * quick tool for internal development,
     * like lorem ipsum for text...
     */
    public static function getNBoxes(int $nbBoxes = 5)
    {

        $cardIds = QuickPdo::fetchAll("select id from ek_product_card limit 0,$nbBoxes", [], \PDO::FETCH_COLUMN);
        if ($cardIds) {

            $sCardIds = implode(', ', $cardIds);
            $sqlQuery = ProductQueryBuilderUtil::getBaseQuery();
            $sqlQuery->addWhere("and c.id in ($sCardIds)");
            $rows = QuickPdo::fetchAll((string)$sqlQuery, $sqlQuery->getMarkers());

            self::sugarifyRows($rows);
            return $rows;
        }
        return [];
    }


    public static function sugarify(array &$row)
    {
        $row['has_tax'] = ('1.00' === $row['tax_ratio']);
        $row['is_novelty'] = (false !== strpos($row['codes'], 'n'));
        $row['product_uri'] = A::link("Ekom_productCardRef", [
            "slug" => $row['product_card_slug'],
            "ref" => $row['reference'],
        ]);
        $row['image'] = ImageLayer::getCardProductImageUriByImageId($row['image_id']);
        $row['image_title'] = (!empty($row['image_legend']) ? $row["image_legend"] : $row['label']);
        $row['image_alt'] = $row["label"];
        $row['has_discount'] = (null !== $row['discount_type']);
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private static function sugarifyRows(array &$rows)
    {
        foreach ($rows as $k => $row) {
            self::sugarify($row);
            $rows[$k] = $row;
        }
    }
}