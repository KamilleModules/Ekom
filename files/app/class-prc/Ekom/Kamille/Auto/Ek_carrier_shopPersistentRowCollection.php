<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use Module\NullosAdmin\FormModel\Control\InputSwitchControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_carrier_shopPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_carrier_shop");
        $this->fields = '
ek_carrier_shop.carrier_id as `ek_carrier_shop.carrier_id`,
ek_carrier_shop.shop_id as `ek_carrier_shop.shop_id`,
ek_carrier_shop.active as `ek_carrier_shop.active`,
ek_carrier.id as `ek_carrier.id`,
ek_shop.label as `ek_shop.label`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_carrier_shop
inner join kamille.ek_carrier on kamille.ek_carrier.id=ek_carrier_shop.carrier_id
inner join kamille.ek_shop on kamille.ek_shop.id=ek_carrier_shop.shop_id
';
    }


    public function getRic()
    {
        return [
    'carrier_id',
    'shop_id',
    'active',
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
			->setTests("shop_id", "shop_id", [
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
            ->addControl("shop_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_shop')
                 
                ->label("shop_id")
                ->name("shop_id")
            )
            ->addControl("active", InputSwitchControl::create()
                ->label("active")
                ->name("active")
                ->addHtmlAttribute("value", "1")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}