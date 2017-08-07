<?php


namespace Module\Ekom\Models\Iterator;


trait IteratorTrait
{


    //--------------------------------------------
    // Iterator
    //--------------------------------------------
    public function __construct($givenArray)
    {
        $this->myArray = $givenArray;
    }

    public function rewind()
    {
        return reset($this->myArray);
    }

    public function current()
    {
        return current($this->myArray);
    }

    public function key()
    {
        return key($this->myArray);
    }

    public function next()
    {
        return next($this->myArray);
    }

    public function valid()
    {
        return key($this->myArray) !== null;
    }

}