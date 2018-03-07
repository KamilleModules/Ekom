<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use Module\NullosAdmin\FormModel\Control\ColorInputTextControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_order_status_shopPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_order_status_shop");
        $this->fields = '
ek_order_status_shop.order_status_id as `ek_order_status_shop.order_status_id`,
ek_order_status_shop.shop_id as `ek_order_status_shop.shop_id`,
ek_order_status_shop.color as `ek_order_status_shop.color`,
ek_order_status.label as `ek_order_status.label`,
ek_shop.label as `ek_shop.label`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_order_status_shop
inner join kamille.ek_order_status on kamille.ek_order_status.id=ek_order_status_shop.order_status_id
inner join kamille.ek_shop on kamille.ek_shop.id=ek_order_status_shop.shop_id
';
    }


    public function getRic()
    {
        return [
    'order_status_id',
    'shop_id',
    'color',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("order_status_id", "order_status_id", [
                RequiredControlTest::create(),
            ])
			->setTests("shop_id", "shop_id", [
                RequiredControlTest::create(),
            ])
			->setTests("color", "color", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("order_status_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_order_status')
                 
                ->label("order_status_id")
                ->name("order_status_id")
            )
            ->addControl("shop_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_shop')
                 
                ->label("shop_id")
                ->name("shop_id")
            )
            ->addControl("color", ColorInputTextControl::create()
                ->label("color")
                ->addHtmlAttribute("value", "#c00")
                ->name("color")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}