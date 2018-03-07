<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_discount_rule_has_actionPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_discount_rule_has_action");
        $this->fields = '
ek_discount_rule_has_action.discount_rule_id as `ek_discount_rule_has_action.discount_rule_id`,
ek_discount_rule_has_action.action_id as `ek_discount_rule_has_action.action_id`,
ek_discount_rule.type as `ek_discount_rule.type`,
ek_action.source as `ek_action.source`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_discount_rule_has_action
inner join kamille.ek_action on kamille.ek_action.id=ek_discount_rule_has_action.action_id
inner join kamille.ek_discount_rule on kamille.ek_discount_rule.id=ek_discount_rule_has_action.discount_rule_id
';
    }


    public function getRic()
    {
        return [
    'discount_rule_id',
    'action_id',
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
			->setTests("action_id", "action_id", [
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