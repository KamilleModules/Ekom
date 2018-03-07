<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use FormModel\Control\InputTextControl;
use Module\NullosAdmin\FormModel\Control\InputSwitchControl;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_categoryPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_category");
        $this->fields = '
ek_category.id as `ek_category.id`,
ek_category.label as `ek_category.label`,
ek_category.is_active as `ek_category.is_active`,
ek_shop.label as `ek_shop.label`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_category
inner join kamille.ek_shop on kamille.ek_shop.id=ek_category.shop_id
left join kamille.ek_category on kamille.ek_category.id=ek_category.category_id
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
			->setTests("shop_id", "shop_id", [
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
            ->addControl("is_active", InputSwitchControl::create()
                ->label("is_active")
                ->name("is_active")
                ->addHtmlAttribute("value", "1")
            )
            ->addControl("shop_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_shop')
                 
                ->label("shop_id")
                ->name("shop_id")
            )
            ->addControl("category_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, label from kamille.ek_category')
                ->firstOption("Please choose an option", 0) 
                ->label("category_id")
                ->name("category_id")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return 'id';
    }
}