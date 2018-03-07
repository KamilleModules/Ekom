<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use FormModel\Control\InputTextControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_actionPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_action");
        $this->fields = '
ek_action.id as `ek_action.id`,
ek_action.source as `ek_action.source`,
ek_action.source2 as `ek_action.source2`,
ek_action.operator as `ek_action.operator`,
ek_action.target as `ek_action.target`,
ek_action.target2 as `ek_action.target2`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_action
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
			->setTests("source", "source", [
                RequiredControlTest::create(),
            ])
			->setTests("operator", "operator", [
                RequiredControlTest::create(),
            ])
			->setTests("target", "target", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("source", InputTextControl::create()
                ->label("source")
                ->name("source")
            )
            ->addControl("source2", InputTextControl::create()
                ->label("source2")
                ->name("source2")
            )
            ->addControl("operator", InputTextControl::create()
                ->label("operator")
                ->name("operator")
            )
            ->addControl("target", InputTextControl::create()
                ->label("target")
                ->name("target")
            )
            ->addControl("target2", InputTextControl::create()
                ->label("target2")
                ->name("target2")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return 'id';
    }
}