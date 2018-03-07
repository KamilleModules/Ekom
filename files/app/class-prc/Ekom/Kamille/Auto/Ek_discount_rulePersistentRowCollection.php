<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use FormModel\Control\InputTextControl;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_discount_rulePersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_discount_rule");
        $this->fields = '
ek_discount_rule.id as `ek_discount_rule.id`,
ek_discount_rule.type as `ek_discount_rule.type`,
ek_shop.label as `ek_shop.label`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_discount_rule
inner join kamille.ek_shop on kamille.ek_shop.id=ek_discount_rule.shop_id
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
			->setTests("shop_id", "shop_id", [
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
            ->addControl("shop_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_shop')
                 
                ->label("shop_id")
                ->name("shop_id")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return 'id';
    }
}