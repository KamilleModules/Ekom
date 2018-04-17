<?php


namespace Module\Ekom\SlashList;


use Module\Ekom\Api\Layer\MiniProductBoxLayer;
use SlashList\SlashListUtil;

class EkomSlashListUtil extends SlashListUtil
{
    protected static function decorateDefaultOptions(array &$options)
    {

        $boxType = $options["boxType"] ?? "mini";
        $sortType = $options["sortType"] ?? "default";


        if (false === array_key_exists("rowDecorator", $options)) {
            if ('mini' === $boxType) {
                $options['rowDecorator'] = function (array &$row) {
                    MiniProductBoxLayer::sugarify($row);
                };
            }
        }

        if (false === array_key_exists("sort", $options)) {
            if ("default" === $sortType) {
                $options['sort'] = [
                    "label_asc" => "Nom ascendant",
                    "label_desc" => "Nom descendant",
                    "price_asc" => "Prix ascendant",
                    "price_desc" => "Prix descendant",
                    "popularity_desc" => "Popularité",
                ];
                $options['defaultSort'] = "label_asc";
            }
        }
    }
}