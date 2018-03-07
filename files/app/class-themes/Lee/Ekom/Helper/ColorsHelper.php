<?php


namespace Theme\Lee\Ekom\Helper;


class ColorsHelper
{

    private static $colors = [
        'gris' => '#ccc',
        'taupe' => '#433e34',
        'beige' => '#FAEBD7',
        'blanc' => '#000000',
        'blanc_casse' => '#FEFEE2',
        'rouge' => '#cc0000',
        'noir' => '#000000',
        'camel' => '#cc9966',
        'orange' => '#ED7F10',
        'bleu' => '#0000cc',
        'vert' => '#00cc00',
        'jaune' => '#FFFF00',
        'marron' => '#582900',
        'rose' => '#FD6C9E',
        'violet' => '#660099',
    ];

    public static function getColorLabel($value)
    {
        if (array_key_exists($value, self::$colors)) {
            return self::$colors[$value];
        }
        return $value;
    }


}

