<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use FormModel\Control\InputTextControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_product_referencePersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_product_reference");
        $this->fields = '
ek_product_reference.id as `ek_product_reference.id`,
ek_product_reference.natural_reference as `ek_product_reference.natural_reference`,
ek_product_reference.reference as `ek_product_reference.reference`,
ek_product_reference.weight as `ek_product_reference.weight`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_product_reference
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
			->setTests("natural_reference", "natural_reference", [
                RequiredControlTest::create(),
            ])
			->setTests("reference", "reference", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("natural_reference", InputTextControl::create()
                ->label("natural_reference")
                ->name("natural_reference")
            )
            ->addControl("reference", InputTextControl::create()
                ->label("reference")
                ->name("reference")
            )
            ->addControl("weight", InputTextControl::create()
                ->label("weight")
                ->name("weight")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return 'id';
    }
}