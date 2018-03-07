<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use FormModel\Control\InputTextControl;
use FormModel\Control\TextAreaControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_product_reference_shop_langPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_product_reference_shop_lang");
        $this->fields = '
ek_product_reference_shop_lang.product_reference_shop_id as `ek_product_reference_shop_lang.product_reference_shop_id`,
ek_product_reference_shop_lang.label as `ek_product_reference_shop_lang.label`,
ek_product_reference_shop_lang.description as `ek_product_reference_shop_lang.description`,
ek_product_reference_shop.image as `ek_product_reference_shop.image`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_product_reference_shop_lang
inner join kamille.ek_product_reference_shop on kamille.ek_product_reference_shop.id=ek_product_reference_shop_lang.product_reference_shop_id
';
    }


    public function getRic()
    {
        return [
    'product_reference_shop_id',
    'label',
    'description',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("product_reference_shop_id", "product_reference_shop_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("product_reference_shop_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, image from kamille.ek_product_reference_shop')
                 
                ->label("product_reference_shop_id")
                ->name("product_reference_shop_id")
            )
            ->addControl("label", InputTextControl::create()
                ->label("label")
                ->name("label")
            )
            ->addControl("description", TextAreaControl::create()
                ->label("description")
                ->name("description")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}