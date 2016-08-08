# PhpPrettyPrint
A simple dumper for PHP in a human readable format

[![Build Status](https://travis-ci.org/JunkyPic/php-pretty-print.png)](https://travis-ci.org/JunkyPic/php-pretty-print)

##Instalation

```composer require junky-pic/php-pretty-print dev-master```

##Works the same way as `print_r()` but with more information
###Can traverse, and provide information, on anything

It's meant to be sort of lightweight and not to complicated. Doesn't offer too many features either.
Example:

```<?php
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
```

Will output something like this:

![Alt text](http://i.imgur.com/3fpVTL5.jpg "Output")

It looks readable and that's about it. No collapse option, no other markup or whatever.

##Things of interest:

The settings files is located in the src folder and it looks like so and it's pretty self-explanatory:

```
{
  "theme" : "darkly",
  "pre-tags" : true,
  "remove-git-link": false
}
```

You can create your own themes if you feel the default one sucks. There's not really much to it, it only has half a dozen classes.
