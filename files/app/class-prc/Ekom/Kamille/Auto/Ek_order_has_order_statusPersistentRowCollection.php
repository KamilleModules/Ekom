<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use Module\NullosAdmin\FormModel\Control\DatetimePickerInputTextControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_order_has_order_statusPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_order_has_order_status");
        $this->fields = '
ek_order_has_order_status.order_id as `ek_order_has_order_status.order_id`,
ek_order_has_order_status.order_status_id as `ek_order_has_order_status.order_status_id`,
ek_order.reference as `ek_order.reference`,
ek_order_status.label as `ek_order_status.label`,
ek_order_has_order_status.date as `ek_order_has_order_status.date`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_order_has_order_status
inner join kamille.ek_order on kamille.ek_order.id=ek_order_has_order_status.order_id
inner join kamille.ek_order_status on kamille.ek_order_status.id=ek_order_has_order_status.order_status_id
';
    }


    public function getRic()
    {
        return [
    'order_id',
    'order_status_id',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("order_id", "order_id", [
                RequiredControlTest::create(),
            ])
			->setTests("order_status_id", "order_status_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("order_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, reference from kamille.ek_order')
                 
                ->label("order_id")
                ->name("order_id")
            )
            ->addControl("order_status_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_order_status')
                 
                ->label("order_status_id")
                ->name("order_status_id")
            )
            ->addControl("date", DatetimePickerInputTextControl::create()
                ->injectJsConfigurationKey(['timePicker' => true])
                ->label("date")
                ->name("date")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}