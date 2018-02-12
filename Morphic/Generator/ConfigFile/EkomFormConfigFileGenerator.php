<?php


namespace Module\Ekom\Morphic\Generator\ConfigFile;


class EkomFormConfigFileGenerator extends NullosFormConfigFileGenerator
{
    protected function getBeginStatements(array $operation, array $config = [])
    {
        $s = parent::getBeginStatements($operation, $config);
        $cols = $operation['columns'];
        $hasShop = (in_array("shop_id", $cols));


        if (true === $hasShop) {
            /**
             * If the table contains the shop_id column,
             * we use the contextual var: shop_id.
             *
             * See context variables for more info, in backoffice-brainstorm
             * @link doc/backoffice/backoffice-brainstorm.md
             */
            $s .= $this->line('$shopId = (int)EkomNullosUser::getEkomValue("shop_id");');
        }
        return $s;
    }


}