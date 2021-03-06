<?php



namespace Prc\Ekom\Kamille\Auto;



use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_productPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_product");
        $this->fields = '
ek_product.id as `ek_product.id`,
ek_product_reference.natural_reference as `ek_product_reference.natural_reference`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_product
left join kamille.ek_product_reference on kamille.ek_product_reference.id=ek_product.product_reference_id
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
        
    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("product_reference_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, natural_reference from kamille.ek_product_reference')
                ->firstOption("Please choose an option", 0) 
                ->label("product_reference_id")
                ->name("product_reference_id")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return 'id';
    }
}