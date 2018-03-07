<?php



namespace Prc\Ekom\Kamille\Auto;



use ;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_carrierPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_carrier");
        $this->fields = '
ek_carrier.id as `ek_carrier.id`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_carrier
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
        
    }

    protected function getAutoIncrementedColumn()
    {
        return 'id';
    }
}