<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;
use FormModel\Control\InputTextControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_currency_langPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_currency_lang");
        $this->fields = '
ek_currency_lang.currency_id as `ek_currency_lang.currency_id`,
ek_currency_lang.lang_id as `ek_currency_lang.lang_id`,
ek_currency_lang.name as `ek_currency_lang.name`,
ek_currency.iso_code as `ek_currency.iso_code`,
ek_lang.label as `ek_lang.label`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_currency_lang
inner join kamille.ek_currency on kamille.ek_currency.id=ek_currency_lang.currency_id
inner join kamille.ek_lang on kamille.ek_lang.id=ek_currency_lang.lang_id
';
    }


    public function getRic()
    {
        return [
    'currency_id',
    'lang_id',
    'name',
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
			->setTests("lang_id", "lang_id", [
                RequiredControlTest::create(),
            ])
			->setTests("name", "name", [
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
            ->addControl("lang_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_lang')
                 
                ->label("lang_id")
                ->name("lang_id")
            )
            ->addControl("name", InputTextControl::create()
                ->label("name")
                ->name("name")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}