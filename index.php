<?php
require_once __DIR__ . '/vendor/autoload.php';

$test = [
    'string'  => 'Lorem',
    'integer' => 1,
    'float'   => 1.2,
    'null'    => null,
    'bool'    => true,
    'array'   => [],
    'object1' => function ()
    {
    },
    'object2' => new DateTime(),
];

\JunkyPic\PhpPrettyPrint\PhpPrettyPrint::dump($test);