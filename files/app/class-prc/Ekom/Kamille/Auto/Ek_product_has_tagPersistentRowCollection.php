<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_product_has_tagPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_product_has_tag");
        $this->fields = '
ek_product_has_tag.product_id as `ek_product_has_tag.product_id`,
ek_product_has_tag.tag_id as `ek_product_has_tag.tag_id`,
ek_product.product_reference_id as `ek_product.product_reference_id`,
ek_tag.label as `ek_tag.label`,
ek_shop.label as `ek_shop.label`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_product_has_tag
inner join kamille.ek_product on kamille.ek_product.id=ek_product_has_tag.product_id
inner join kamille.ek_shop on kamille.ek_shop.id=ek_product_has_tag.shop_id
inner join kamille.ek_tag on kamille.ek_tag.id=ek_product_has_tag.tag_id
';
    }


    public function getRic()
    {
        return [
    'product_id',
    'tag_id',
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
			->setTests("tag_id", "tag_id", [
                RequiredControlTest::create(),
            ])
			->setTests("shop_id", "shop_id", [
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
            ->addControl("tag_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_tag')
                 
                ->label("tag_id")
                ->name("tag_id")
            )
            ->addControl("shop_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_shop')
                 
                ->label("shop_id")
                ->name("shop_id")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}