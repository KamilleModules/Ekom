<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_product_has_tax_rulePersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_product_has_tax_rule");
        $this->fields = '
ek_product_has_tax_rule.product_id as `ek_product_has_tax_rule.product_id`,
ek_product_has_tax_rule.tax_rule_id as `ek_product_has_tax_rule.tax_rule_id`,
ek_product.product_reference_id as `ek_product.product_reference_id`,
ek_tax_rule.condition as `ek_tax_rule.condition`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_product_has_tax_rule
inner join kamille.ek_product on kamille.ek_product.id=ek_product_has_tax_rule.product_id
inner join kamille.ek_tax_rule on kamille.ek_tax_rule.id=ek_product_has_tax_rule.tax_rule_id
';
    }


    public function getRic()
    {
        return [
    'product_id',
    'tax_rule_id',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("product_id", "product_id", [
                RequiredControlTest::create(),
            ])
			->setTests("tax_rule_id", "tax_rule_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("product_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, product_reference_id from kamille.ek_product')
                 
                ->label("product_id")
                ->name("product_id")
            )
            ->addControl("tax_rule_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, condition from kamille.ek_tax_rule')
                 
                ->label("tax_rule_id")
                ->name("tax_rule_id")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}