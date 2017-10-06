<?php


namespace Module\Ekom\Api\Link\Features;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use SaveOrmObject\Object\Ek\FeatureLangObject;
use SaveOrmObject\Object\Ek\FeatureObject;
use SaveOrmObject\Object\Ek\FeatureValueLangObject;
use SaveOrmObject\Object\Ek\FeatureValueObject;
use SaveOrmObject\Object\Ek\ProductHasFeatureObject;

class FeaturesLink
{


    public static function create()
    {
        return new static();
    }

    /**
     * @param array $features , array of $featureName => $featureValue|[$featureValue, $position]
     */
    public function saveFeatures($productId, array $features, array $options = [])
    {

        $ret = [];
        $options = array_replace([
            "shop_id" => E::getShopId(),
            "lang_id" => E::getLangId(),
            "technical_description" => '',
        ], $options);


        $shopId = (int)$options['shop_id'];
        $langId = (int)$options['lang_id'];


        foreach ($features as $name => $value) {
            $position = 0;
            if (is_array($value)) {
                list($value, $position) = $value;
            }


            //--------------------------------------------
            // CREATE FEATURE IF NECESSARY
            //--------------------------------------------
            $r = []; // not really used
            $featureId = QuickPdo::fetch("
select feature_id from ek_feature_lang where lang_id=$langId and name=:name            
            ", ['name' => $name], \PDO::FETCH_COLUMN);
            if (false === $featureId) {
                $featureId = FeatureObject::create()->save($r);
            }
            FeatureLangObject::createByNameLangId($name, $langId)->setFeatureId($featureId)->save($r);

            //--------------------------------------------
            // CREATE FEATURE VALUE IF NECESSARY
            //--------------------------------------------
            $featureValueId = QuickPdo::fetch("
select feature_value_id from ek_feature_value_lang where lang_id=$langId and value=:value            
            ", ['value' => $value], \PDO::FETCH_COLUMN);
            if (false === $featureValueId) {
                $featureValueId = FeatureValueObject::create()->setFeatureId($featureId)->save($r);
            }
            FeatureValueLangObject::createByFeatureValueIdLangId($featureValueId, $langId)->setValue($value)->save($r);


            //--------------------------------------------
            // CREATE BINDING WITH PRODUCT
            //--------------------------------------------
            $r = [];
            ProductHasFeatureObject::createByProductIdFeatureIdShopId($productId, $featureId, $shopId)
                ->setFeatureValueId($featureValueId)
                ->setPosition($position)
                ->setTechnicalDescription($options['technical_description'])
                ->save($r);


            $ret[] = [
                'featureId' => $featureId,
                'featureValueId' => $featureValueId,
                'productHasFeature' => $r,
            ];


        }
        return $ret;
    }
}