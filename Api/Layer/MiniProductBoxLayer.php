<?php


namespace Module\Ekom\Api\Layer;

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
 * Oh, I forgot, before we code we need to define what's inside the miniBox model.
 * @see EkomModels::miniBoxModel()
 *
 */
class MiniProductBoxLayer
{


    public static function getBoxesByProductGroupName(string $productGroupName)
    {

        $cardIds = ProductGroupLayer::getRelatedCardIdByGroupName($productGroupName);
        az($cardIds);

        $markers = [];
        $q = ProductQueryBuilderUtil::getBaseQuery( $markers);

        // specific to groups
        $q .= "
where c.id in (9,10,11)        
        ";


        $rows = QuickPdo::fetchAll($q, $markers);
        a($rows);

    }

}