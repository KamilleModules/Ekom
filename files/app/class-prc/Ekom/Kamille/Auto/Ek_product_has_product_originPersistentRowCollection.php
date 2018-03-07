<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_product_has_product_originPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_product_has_product_origin");
        $this->fields = '
ek_product_has_product_origin.product_id as `ek_product_has_product_origin.product_id`,
ek_product_has_product_origin.product_origin_id as `ek_product_has_product_origin.product_origin_id`,
ek_product.product_reference_id as `ek_product.product_reference_id`,
ek_product_origin.type as `ek_product_origin.type`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_product_has_product_origin
inner join kamille.ek_product on kamille.ek_product.id=ek_product_has_product_origin.product_id
inner join kamille.ek_product_origin on kamille.ek_product_origin.id=ek_product_has_product_origin.product_origin_id
';
    }


    public function getRic()
    {
        return [
    'product_id',
    'product_origin_id',
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
			->setTests("product_origin_id", "product_origin_id", [
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
            ->addControl("product_origin_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, type from kamille.ek_product_origin')
                 
                ->label("product_origin_id")
                ->name("product_origin_id")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}