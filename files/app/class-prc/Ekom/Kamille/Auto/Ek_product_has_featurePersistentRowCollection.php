<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_product_has_featurePersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_product_has_feature");
        $this->fields = '
ek_product_has_feature.product_id as `ek_product_has_feature.product_id`,
ek_product_has_feature.feature_id as `ek_product_has_feature.feature_id`,
ek_product.product_reference_id as `ek_product.product_reference_id`,
ek_feature.label as `ek_feature.label`,
ek_feature_value.value as `ek_feature_value.value`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_product_has_feature
inner join kamille.ek_feature on kamille.ek_feature.id=ek_product_has_feature.feature_id
inner join kamille.ek_feature_value on kamille.ek_feature_value.id=ek_product_has_feature.feature_value_id
inner join kamille.ek_product on kamille.ek_product.id=ek_product_has_feature.product_id
';
    }


    public function getRic()
    {
        return [
    'product_id',
    'feature_id',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("product_id", "product_id", [
                RequiredControlTest::create(),
            ])
			->setTests("feature_id", "feature_id", [
                RequiredControlTest::create(),
            ])
			->setTests("feature_value_id", "feature_value_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("product_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, product_reference_id from kamille.ek_product')
                 
                ->label("product_id")
                ->name("product_id")
            )
            ->addControl("feature_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_feature')
                 
                ->label("feature_id")
                ->name("feature_id")
            )
            ->addControl("feature_value_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, value from kamille.ek_feature_value')
                 
                ->label("feature_value_id")
                ->name("feature_value_id")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}