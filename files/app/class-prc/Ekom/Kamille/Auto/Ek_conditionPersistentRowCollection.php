<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use FormModel\Control\InputTextControl;
use Module\NullosAdmin\FormModel\Control\InputSwitchControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_conditionPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_condition");
        $this->fields = '
ek_condition.id as `ek_condition.id`,
ek_condition.type as `ek_condition.type`,
ek_condition.combinator as `ek_condition.combinator`,
ek_condition.negation as `ek_condition.negation`,
ek_condition.start_group as `ek_condition.start_group`,
ek_condition.end_group as `ek_condition.end_group`,
ek_condition.left_operand as `ek_condition.left_operand`,
ek_condition.operator as `ek_condition.operator`,
ek_condition.right_operand as `ek_condition.right_operand`,
ek_condition.right_operand2 as `ek_condition.right_operand2`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_condition
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
			->setTests("type", "type", [
                RequiredControlTest::create(),
            ])
			->setTests("combinator", "combinator", [
                RequiredControlTest::create(),
            ])
			->setTests("left_operand", "left_operand", [
                RequiredControlTest::create(),
            ])
			->setTests("operator", "operator", [
                RequiredControlTest::create(),
            ])
			->setTests("right_operand", "right_operand", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("type", InputTextControl::create()
                ->label("type")
                ->name("type")
            )
            ->addControl("combinator", InputTextControl::create()
                ->label("combinator")
                ->name("combinator")
            )
            ->addControl("negation", InputSwitchControl::create()
                ->label("negation")
                ->name("negation")
                ->addHtmlAttribute("value", "1")
            )
            ->addControl("start_group", InputSwitchControl::create()
                ->label("start_group")
                ->name("start_group")
                ->addHtmlAttribute("value", "1")
            )
            ->addControl("end_group", InputSwitchControl::create()
                ->label("end_group")
                ->name("end_group")
                ->addHtmlAttribute("value", "1")
            )
            ->addControl("left_operand", InputTextControl::create()
                ->label("left_operand")
                ->name("left_operand")
            )
            ->addControl("operator", InputTextControl::create()
                ->label("operator")
                ->name("operator")
            )
            ->addControl("right_operand", InputTextControl::create()
                ->label("right_operand")
                ->name("right_operand")
            )
            ->addControl("right_operand2", InputTextControl::create()
                ->label("right_operand2")
                ->name("right_operand2")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return 'id';
    }
}