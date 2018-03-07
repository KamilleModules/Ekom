<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_discount_rule_has_conditionPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_discount_rule_has_condition");
        $this->fields = '
ek_discount_rule_has_condition.discount_rule_id as `ek_discount_rule_has_condition.discount_rule_id`,
ek_discount_rule_has_condition.condition_id as `ek_discount_rule_has_condition.condition_id`,
ek_discount_rule.type as `ek_discount_rule.type`,
ek_condition.type as `ek_condition.type`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_discount_rule_has_condition
inner join kamille.ek_condition on kamille.ek_condition.id=ek_discount_rule_has_condition.condition_id
inner join kamille.ek_discount_rule on kamille.ek_discount_rule.id=ek_discount_rule_has_condition.discount_rule_id
';
    }


    public function getRic()
    {
        return [
    'discount_rule_id',
    'condition_id',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("discount_rule_id", "discount_rule_id", [
                RequiredControlTest::create(),
            ])
			->setTests("condition_id", "condition_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("discount_rule_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, type from kamille.ek_discount_rule')
                 
                ->label("discount_rule_id")
                ->name("discount_rule_id")
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