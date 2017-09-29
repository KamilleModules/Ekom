<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;

class SpecialCategoryLayer
{


    public function getSpecialCategoryModel($type)
    {

        /**
         * allowed types:
         * - equipement
         * - formation
         * - events
         * - conseil_communication
         */

        $uriImg = E::getImgBaseUri();
        $cats = EkomApi::inst()->categoryLayer()->getSubCategoriesByName($type, 0);
        $tCats = [];
        foreach ($cats as $cat) {
            $cat['imgUri'] = $uriImg . "/category/" . $cat['name'] . ".jpg";
            $tCats[] = $cat;
        }

        $conf = [
            "cats" => $tCats,
        ];


        if ('events' === $type) {
            $conf['pou'] = 6;
        }


        return $conf;
    }

}