<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_carrier_has_actionPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_carrier_has_action");
        $this->fields = '
ek_carrier_has_action.carrier_id as `ek_carrier_has_action.carrier_id`,
ek_carrier_has_action.action_id as `ek_carrier_has_action.action_id`,
ek_carrier.id as `ek_carrier.id`,
ek_action.source as `ek_action.source`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_carrier_has_action
inner join kamille.ek_action on kamille.ek_action.id=ek_carrier_has_action.action_id
inner join kamille.ek_carrier on kamille.ek_carrier.id=ek_carrier_has_action.carrier_id
';
    }


    public function getRic()
    {
        return [
    'carrier_id',
    'action_id',
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
			->setTests("action_id", "action_id", [
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
            ->addControl("action_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, source from kamille.ek_action')
                 
                ->label("action_id")
                ->name("action_id")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}