<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use FormModel\Control\InputTextControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_discount_rule_langPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_discount_rule_lang");
        $this->fields = '
ek_discount_rule_lang.discount_rule_id as `ek_discount_rule_lang.discount_rule_id`,
ek_discount_rule_lang.label as `ek_discount_rule_lang.label`,
ek_discount_rule_lang.lang_id as `ek_discount_rule_lang.lang_id`,
ek_discount_rule.type as `ek_discount_rule.type`,
ek_lang.label as `ek_lang.label`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_discount_rule_lang
inner join kamille.ek_discount_rule on kamille.ek_discount_rule.id=ek_discount_rule_lang.discount_rule_id
inner join kamille.ek_lang on kamille.ek_lang.id=ek_discount_rule_lang.lang_id
';
    }


    public function getRic()
    {
        return [
    'discount_rule_id',
    'label',
    'lang_id',
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
			->setTests("lang_id", "lang_id", [
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
            ->addControl("label", InputTextControl::create()
                ->label("label")
                ->name("label")
            )
            ->addControl("lang_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_lang')
                 
                ->label("lang_id")
                ->name("lang_id")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}