<?php

use Kamille\Ling\Z;

$appDir = Z::appDir();


$profile = [
    "maxFileSize" => "10",
//  "acceptedFiles" => [
//      'application/pdf',
//  ],
    "targetDir" => $appDir . "/www/img/products",
];