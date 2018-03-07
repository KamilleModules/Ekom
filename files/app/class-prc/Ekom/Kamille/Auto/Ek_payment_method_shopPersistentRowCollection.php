<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use Module\NullosAdmin\FormModel\Control\InputSwitchControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_payment_method_shopPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_payment_method_shop");
        $this->fields = '
ek_payment_method_shop.payment_method_id as `ek_payment_method_shop.payment_method_id`,
ek_payment_method_shop.shop_id as `ek_payment_method_shop.shop_id`,
ek_payment_method_shop.active as `ek_payment_method_shop.active`,
ek_payment_method.label as `ek_payment_method.label`,
ek_shop.label as `ek_shop.label`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_payment_method_shop
inner join kamille.ek_payment_method on kamille.ek_payment_method.id=ek_payment_method_shop.payment_method_id
inner join kamille.ek_shop on kamille.ek_shop.id=ek_payment_method_shop.shop_id
';
    }


    public function getRic()
    {
        return [
    'payment_method_id',
    'shop_id',
    'active',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("payment_method_id", "payment_method_id", [
                RequiredControlTest::create(),
            ])
			->setTests("shop_id", "shop_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("payment_method_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_payment_method')
                 
                ->label("payment_method_id")
                ->name("payment_method_id")
            )
            ->addControl("shop_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_shop')
                 
                ->label("shop_id")
                ->name("shop_id")
            )
            ->addControl("active", InputSwitchControl::create()
                ->label("active")
                ->name("active")
                ->addHtmlAttribute("value", "1")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}