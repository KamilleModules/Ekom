<?php


namespace Module\Ekom\View\Cart\Cartoon\Util;


class BaseRenderer
{

    protected $columns;

    public function __construct()
    {
        $this->columns = [];
    }

    public static function create()
    {
        return new static();
    }


    public function setColumns(array $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    protected function has($columnName)
    {
        return in_array($columnName, $this->columns, true);
    }

}