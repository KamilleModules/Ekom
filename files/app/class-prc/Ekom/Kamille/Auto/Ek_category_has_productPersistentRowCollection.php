<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use FormModel\Control\InputTextControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_category_has_productPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_category_has_product");
        $this->fields = '
ek_category_has_product.category_id as `ek_category_has_product.category_id`,
ek_category_has_product.product_id as `ek_category_has_product.product_id`,
ek_category.label as `ek_category.label`,
ek_product.product_reference_id as `ek_product.product_reference_id`,
ek_category_has_product.order as `ek_category_has_product.order`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_category_has_product
inner join kamille.ek_category on kamille.ek_category.id=ek_category_has_product.category_id
inner join kamille.ek_product on kamille.ek_product.id=ek_category_has_product.product_id
';
    }


    public function getRic()
    {
        return [
    'category_id',
    'product_id',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("category_id", "category_id", [
                RequiredControlTest::create(),
            ])
			->setTests("product_id", "product_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("category_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_category')
                 
                ->label("category_id")
                ->name("category_id")
            )
            ->addControl("product_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, product_reference_id from kamille.ek_product')
                 
                ->label("product_id")
                ->name("product_id")
            )
            ->addControl("order", InputTextControl::create()
                ->label("order")
                ->name("order")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}