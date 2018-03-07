<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use FormModel\Control\InputTextControl;
use Module\NullosAdmin\FormModel\Control\DatetimePickerInputTextControl;
use FormModel\Control\TextAreaControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_orderPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_order");
        $this->fields = '
ek_order.id as `ek_order.id`,
ek_user.email as `ek_user.email`,
ek_order.reference as `ek_order.reference`,
ek_order.date as `ek_order.date`,
ek_order.tracking_number as `ek_order.tracking_number`,
ek_order.user_info as `ek_order.user_info`,
ek_order.shop_info as `ek_order.shop_info`,
ek_order.shipping_address as `ek_order.shipping_address`,
ek_order.billing_address as `ek_order.billing_address`,
ek_order.order_details as `ek_order.order_details`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_order
inner join kamille.ek_user on kamille.ek_user.id=ek_order.user_id
';
    }


    public function getRic()
    {
        return [
    'id',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("user_id", "user_id", [
                RequiredControlTest::create(),
            ])
			->setTests("reference", "reference", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("user_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, email from kamille.ek_user')
                 
                ->label("user_id")
                ->name("user_id")
            )
            ->addControl("reference", InputTextControl::create()
                ->label("reference")
                ->name("reference")
            )
            ->addControl("date", DatetimePickerInputTextControl::create()
                ->injectJsConfigurationKey(['timePicker' => true])
                ->label("date")
                ->name("date")
            )
            ->addControl("tracking_number", InputTextControl::create()
                ->label("tracking_number")
                ->name("tracking_number")
            )
            ->addControl("user_info", TextAreaControl::create()
                ->label("user_info")
                ->name("user_info")
            )
            ->addControl("shop_info", TextAreaControl::create()
                ->label("shop_info")
                ->name("shop_info")
            )
            ->addControl("shipping_address", TextAreaControl::create()
                ->label("shipping_address")
                ->name("shipping_address")
            )
            ->addControl("billing_address", TextAreaControl::create()
                ->label("billing_address")
                ->name("billing_address")
            )
            ->addControl("order_details", TextAreaControl::create()
                ->label("order_details")
                ->name("order_details")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return 'id';
    }
}