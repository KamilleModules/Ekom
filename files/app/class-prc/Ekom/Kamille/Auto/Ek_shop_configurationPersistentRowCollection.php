<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use FormModel\Control\InputTextControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_shop_configurationPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_shop_configuration");
        $this->fields = '
ek_shop_configuration.shop_id as `ek_shop_configuration.shop_id`,
ek_shop_configuration.key as `ek_shop_configuration.key`,
ek_shop_configuration.value as `ek_shop_configuration.value`,
ek_shop.label as `ek_shop.label`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_shop_configuration
inner join kamille.ek_shop on kamille.ek_shop.id=ek_shop_configuration.shop_id
';
    }


    public function getRic()
    {
        return [
    'shop_id',
    'key',
    'value',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("shop_id", "shop_id", [
                RequiredControlTest::create(),
            ])
			->setTests("key", "key", [
                RequiredControlTest::create(),
            ])
			->setTests("value", "value", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("shop_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_shop')
                 
                ->label("shop_id")
                ->name("shop_id")
            )
            ->addControl("key", InputTextControl::create()
                ->label("key")
                ->name("key")
            )
            ->addControl("value", InputTextControl::create()
                ->label("value")
                ->name("value")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}