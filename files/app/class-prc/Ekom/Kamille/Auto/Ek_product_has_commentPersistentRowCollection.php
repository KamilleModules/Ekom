<?php



namespace Prc\Ekom\Kamille\Auto;



use FormModel\Validation\ControlTest\WithFields\RequiredControlTest;
use Module\NullosAdmin\FormModel\Control\SqlQuerySelectControl;

use FormModel\FormModel;
use FormModel\Validation\ControlsValidator\ControlsValidator;
use Module\NullosAdmin\PersistentRowCollection\NullosQuickPdoPersistentRowCollection;


class Ek_product_has_commentPersistentRowCollection extends NullosQuickPdoPersistentRowCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("kamille.ek_product_has_comment");
        $this->fields = '
ek_product_has_comment.product_id as `ek_product_has_comment.product_id`,
ek_product_has_comment.comment_id as `ek_product_has_comment.comment_id`,
ek_product.product_reference_id as `ek_product.product_reference_id`,
ek_comment.active as `ek_comment.active`
';
        $this->query = '
SELECT
%s
FROM kamille.ek_product_has_comment
inner join kamille.ek_comment on kamille.ek_comment.id=ek_product_has_comment.comment_id
inner join kamille.ek_product on kamille.ek_product.id=ek_product_has_comment.product_id
';
    }


    public function getRic()
    {
        return [
    'product_id',
    'comment_id',
];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorateFormModelValidator(ControlsValidator $validator)
    {
        $validator
			->setTests("product_id", "product_id", [
                RequiredControlTest::create(),
            ])
			->setTests("comment_id", "comment_id", [
                RequiredControlTest::create(),
            ]);

    }

    protected function decorateFormModel(FormModel $model)
    {
        $model
            ->addControl("product_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, product_reference_id from kamille.ek_product')
                 
                ->label("product_id")
                ->name("product_id")
            )
            ->addControl("comment_id", SqlQuerySelectControl::create()
                //->multiple()
                ->query('select id, active from kamille.ek_comment')
                 
                ->label("comment_id")
                ->name("comment_id")
            );

    }

    protected function getAutoIncrementedColumn()
    {
        return null;
    }
}