<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

$a = [
    20 => [
        'first',
        'second' => [
            2, 3, 4, 5, 'fouth',
        ],
    ],
];
echo '<pre>';

\JunkyPic\PhpPrettyPrint\PhpPrettyPrint::dump(new DateTime());
