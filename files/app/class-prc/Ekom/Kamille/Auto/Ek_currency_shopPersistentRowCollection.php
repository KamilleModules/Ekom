<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use FormModel\Control\InputTextControl;
use Module\NullosAdmin\FormModel\Control\InputSwitchControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_currency_shopPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_currency_shop");
        $this->fields = '
ek_currency_shop.id as `ek_currency_shop.id`,
ek_currency.iso_code as `ek_currency.iso_code`,
ek_shop.label as `ek_shop.label`,
ek_currency_shop.exchange_rate as `ek_currency_shop.exchange_rate`,
ek_currency_shop.active as `ek_currency_shop.active`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_currency_shop
inner join kamille.ek_currency on kamille.ek_currency.id=ek_currency_shop.currency_id
inner join kamille.ek_shop on kamille.ek_shop.id=ek_currency_shop.shop_id
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
			->setTests("currency_id", "currency_id", [
                RequiredControlTest::create(),
            ])
			->setTests("shop_id", "shop_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("currency_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, iso_code from kamille.ek_currency')
                 
                ->label("currency_id")
                ->name("currency_id")
            )
            ->addControl("shop_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_shop')
                 
                ->label("shop_id")
                ->name("shop_id")
            )
            ->addControl("exchange_rate", InputTextControl::create()
                ->label("exchange_rate")
                ->name("exchange_rate")
            )
            ->addControl("active", InputSwitchControl::create()
                ->label("active")
                ->name("active")
                ->addHtmlAttribute("value", "1")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return 'id';
    }
}