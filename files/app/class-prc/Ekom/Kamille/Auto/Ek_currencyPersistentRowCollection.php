<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use FormModel\Control\InputTextControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_currencyPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_currency");
        $this->fields = '
ek_currency.id as `ek_currency.id`,
ek_currency.iso_code as `ek_currency.iso_code`,
ek_currency.symbol as `ek_currency.symbol`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_currency
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
			->setTests("iso_code", "iso_code", [
                RequiredControlTest::create(),
            ])
			->setTests("symbol", "symbol", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("iso_code", InputTextControl::create()
                ->label("iso_code")
                ->name("iso_code")
            )
            ->addControl("symbol", InputTextControl::create()
                ->label("symbol")
                ->name("symbol")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return 'id';
    }
}