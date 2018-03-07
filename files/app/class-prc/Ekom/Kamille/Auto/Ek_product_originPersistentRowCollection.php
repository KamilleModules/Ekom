<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use FormModel\Control\InputTextControl;
use Module\NullosAdmin\FormModel\Control\DropZoneControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_product_originPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_product_origin");
        $this->fields = '
ek_product_origin.id as `ek_product_origin.id`,
ek_product_origin.type as `ek_product_origin.type`,
ek_product_origin.value as `ek_product_origin.value`,
ek_product_origin.image as `ek_product_origin.image`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_product_origin
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
			->setTests("value", "value", [
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
            ->addControl("value", InputTextControl::create()
                ->label("value")
                ->name("value")
            )
            ->addControl("image", DropZoneControl::create()
                ->setShowDeleteLink(true)
                ->setProfileId("Ekom/kamille.ek_product_origin.image")            
                ->label("image")
                ->name("image")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return 'id';
    }
}