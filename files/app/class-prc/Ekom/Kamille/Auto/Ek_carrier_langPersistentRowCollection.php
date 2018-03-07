<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use FormModel\Control\InputTextControl;
use FormModel\Control\TextAreaControl;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_carrier_langPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_carrier_lang");
        $this->fields = '
ek_carrier_lang.id as `ek_carrier_lang.id`,
ek_carrier_lang.label as `ek_carrier_lang.label`,
ek_carrier_lang.description as `ek_carrier_lang.description`,
ek_carrier.id as `ek_carrier.id`,
ek_lang.label as `ek_lang.label`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_carrier_lang
inner join kamille.ek_carrier on kamille.ek_carrier.id=ek_carrier_lang.carrier_id
inner join kamille.ek_lang on kamille.ek_lang.id=ek_carrier_lang.lang_id
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
			->setTests("label", "label", [
                RequiredControlTest::create(),
            ])
			->setTests("carrier_id", "carrier_id", [
                RequiredControlTest::create(),
            ])
			->setTests("lang_id", "lang_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("label", InputTextControl::create()
                ->label("label")
                ->name("label")
            )
            ->addControl("description", TextAreaControl::create()
                ->label("description")
                ->name("description")
            )
            ->addControl("carrier_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, id from kamille.ek_carrier')
                 
                ->label("carrier_id")
                ->name("carrier_id")
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
        return 'id';
    }
}