<?php


namespace Module\Ekom\ProductSearch;


use Bat\FileSystemTool;
use Bat\StringTool;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

abstract class AbstractProductSearch implements ProductSearchInterface
{

    abstract protected function doGetResults($query);


    public static function create()
    {
        return new static();
    }

    /**
     * @param $query
     * @return array, each entry being an entry with the following keys:
     *          - value: the label to display
     *          - data: the uri to the product or product card
     *
     */
    public function getResults($query = "")
    {
        $query = StringTool::removeAccents($query);
        $query = FileSystemTool::noEscalating($query);
        $query = strtolower($query);


        return $this->doGetResults($query);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function decorate($string, $query)
    {
        $pattern = '!' . str_replace(' ', '|', $query) . '!i';
        return preg_replace($pattern, "<b>\\0</b>", $string);
    }


}

