<?php


namespace Module\Ekom\Utils;


use Bat\FileSystemTool;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;


class CartLocalStore
{

    private $dir;

    public function __construct()
    {
        $this->dir = ApplicationParameters::get("app_dir") . "/data/Ekom/carts";
    }


    public function setDir($dir)
    {
        $this->dir = $dir;
        return $this;
    }

    public function getUserCart($userId, $shopId)
    {
        $f = $this->dir . "/" . $this->hash($userId) . "-$shopId.php";
        if (file_exists($f)) {
            $ret = unserialize(file_get_contents($f));
        } else {
            $ret = [];
        }
        return $ret;
    }

    public function saveUserCart($userId, $shopId, array $cart)
    {
        $f = $this->dir . "/" . $this->hash($userId) . "-$shopId.php";
        FileSystemTool::mkfile($f, serialize($cart));
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function hash($userId)
    {
        return implode('/', str_split($userId));
    }
}