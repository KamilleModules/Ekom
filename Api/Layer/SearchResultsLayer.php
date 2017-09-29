<?php


namespace Module\Ekom\Api\Layer;


use Kamille\Architecture\Registry\ApplicationRegistry;
use ListParams\ListParams;
use ListParams\Model\QueryDecorator;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\ListParams\ListBundleFactory\EkomListBundleFactoryHelper;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class SearchResultsLayer
{

    /**
     * Here we do a search by card,
     * and we assume that the ek_shop_has_product_lang.label
     * are filled.
     *
     *
     * @return array
     */
    public function getModel()
    {
        $player = EkomApi::inst()->productLayer();
        $params = ListParams::create();
        $params->infuse();


        $search = "";
        $nbProducts = 0;
        $productBoxModels = [];
        $shopId = E::getShopId();
        $langId = E::getLangId();


        if (array_key_exists('search', $_GET)) {
            $search = $_GET['search'];
            ApplicationRegistry::set("ekom.breacrumbs.label", "Votre résultat de recherche pour \"$search\"");


            $q = "
select cl.product_card_id 

from ek_shop_has_product_card_lang cl 
inner join ek_shop_has_product_card c on c.shop_id=cl.shop_id and c.product_card_id=cl.product_card_id
inner join ek_shop_has_product p on p.shop_id= c.shop_id and p.product_id=c.product_id
  
where cl.shop_id=$shopId 
and cl.lang_id=$langId 
and cl.label like :label            
            ";
            $q2 = str_replace('select cl.product_card_id', 'select count(*) as count', $q);
            $markers = [];

            QueryDecorator::create()
                ->setAllowedSortFields([
                    'p._sale_price_with_tax' => 'price',
                    'cl.label' => 'label',
                ])
                ->setAllowNipp(false)// no pagination (at least that's the intent)
                ->decorate($q, $q2, $markers, $params);


            $cardIds = QuickPdo::fetchAll($q, [
                'label' => '%' . str_replace('%', '\%', $search) . '%',
            ], \PDO::FETCH_COLUMN);

            foreach ($cardIds as $cardId) {
                $productBoxModels[] = $player->getProductBoxModelByCardId($cardId);
            }
            $nbProducts = count($productBoxModels);

        }

        $list = EkomListBundleFactoryHelper::getListBundleByItemsParams($productBoxModels, $params);

        return [
            "search" => $search,
            "nbProducts" => $nbProducts,
            "listBundle" => $list,
        ];
    }
}
