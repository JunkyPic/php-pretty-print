<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php';

$test = [
    'string'  => 'Lorem',
    'integer' => 1,
    'float'   => 1.2,
    'null'    => null,
    'bool'    => true,
    'array'   => [],
    'object2' => new DateTime(),
    'object1' => function ()
    {
    },
];

\JunkyPic\PhpPrettyPrint\PhpPrettyPrint::dump($test);