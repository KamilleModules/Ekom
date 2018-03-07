<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use FormModel\Control\InputTextControl;
use FormModel\Control\TextAreaControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_product_langPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_product_lang");
        $this->fields = '
ek_product_lang.product_id as `ek_product_lang.product_id`,
ek_product_lang.shop_id as `ek_product_lang.shop_id`,
ek_product.product_reference_id as `ek_product.product_reference_id`,
ek_shop.label as `ek_shop.label`,
ek_product_lang.label as `ek_product_lang.label`,
ek_product_lang.description as `ek_product_lang.description`,
ek_product_lang.slug as `ek_product_lang.slug`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_product_lang
inner join kamille.ek_product on kamille.ek_product.id=ek_product_lang.product_id
inner join kamille.ek_shop on kamille.ek_shop.id=ek_product_lang.shop_id
';
    }


    public function getRic()
    {
        return [
    'product_id',
    'shop_id',
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
			->setTests("shop_id", "shop_id", [
                RequiredControlTest::create(),
            ])
			->setTests("label", "label", [
                RequiredControlTest::create(),
            ])
			->setTests("slug", "slug", [
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
            ->addControl("shop_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_shop')
                 
                ->label("shop_id")
                ->name("shop_id")
            )
            ->addControl("label", InputTextControl::create()
                ->label("label")
                ->name("label")
            )
            ->addControl("description", TextAreaControl::create()
                ->label("description")
                ->name("description")
            )
            ->addControl("slug", InputTextControl::create()
                ->label("slug")
                ->name("slug")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}