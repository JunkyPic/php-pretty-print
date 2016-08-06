<?php
require_once __DIR__ . '/vendor/autoload.php';

$testArray = [
    'Lorem',
    'ipsum',
    1,
    'dolor' => [
        'sit',
        3
    ],
    4 => [
        '3' => 4
    ]
];

\JunkyPic\PhpPrettyPrint\PhpPrettyPrint::dump($testArray);

