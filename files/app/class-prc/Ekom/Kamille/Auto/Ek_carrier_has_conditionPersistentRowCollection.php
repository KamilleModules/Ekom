<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_carrier_has_conditionPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_carrier_has_condition");
        $this->fields = '
ek_carrier_has_condition.carrier_id as `ek_carrier_has_condition.carrier_id`,
ek_carrier_has_condition.condition_id as `ek_carrier_has_condition.condition_id`,
ek_carrier.id as `ek_carrier.id`,
ek_condition.type as `ek_condition.type`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_carrier_has_condition
inner join kamille.ek_carrier on kamille.ek_carrier.id=ek_carrier_has_condition.carrier_id
inner join kamille.ek_condition on kamille.ek_condition.id=ek_carrier_has_condition.condition_id
';
    }


    public function getRic()
    {
        return [
    'carrier_id',
    'condition_id',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("carrier_id", "carrier_id", [
                RequiredControlTest::create(),
            ])
			->setTests("condition_id", "condition_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("carrier_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, id from kamille.ek_carrier')
                 
                ->label("carrier_id")
                ->name("carrier_id")
            )
            ->addControl("condition_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, type from kamille.ek_condition')
                 
                ->label("condition_id")
                ->name("condition_id")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}