# PhpPrettyPrint
A simple dumper for PHP in a human readable format

[![Build Status](https://travis-ci.org/JunkyPic/php-pretty-print.png)](https://travis-ci.org/JunkyPic/php-pretty-print)

#THIS IS IN ALPHA. EXPECT THINGS TO BREAK. OFTEN!

##Instalation

```composer require junky-pic/php-pretty-print dev-master```

##Works the same way as `print_r()` but with more information
###Can traverse, and provide information, on anything

It's meant to be sort of lightweight and not to complicated. Doesn't offer too many features either.
Example:

```<?php
require_once __DIR__ . '/vendor/autoload.php';

$test = [
    'string'             => 'Lorem',
    'integer'            => 1,
    'double'             => 1.2,
    'null'               => null,
    'bool'               => true,
    'array'              => [],
    'really_long_string' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras viverra a eros eu sodales. Pellentesque ut leo sapien. Vestibulum sit amet nisl eget ante iaculis consectetur. Nulla orci quam, porttitor a ullamcorper pretium, mollis non urna. In hac habitasse platea dictumst. Nunc et libero nec ex condimentum finibus. Nulla vehicula ultricies faucibus. Maecenas vel enim sed est dignissim accumsan condimentum ut ipsum. Donec viverra sagittis elementum. Fusce non hendrerit neque. Vestibulum vel diam libero.
Maecenas lobortis dolor libero, quis lacinia mauris scelerisque nec. Pellentesque consectetur condimentum justo vel aliquet. In nisl est, feugiat a elementum id, varius id est. Suspendisse potenti. Fusce volutpat velit eu dolor egestas varius. Curabitur lacus lorem, volutpat eget varius non, dignissim eget velit. Praesent bibendum vel justo et facilisis. Nullam id urna tempor, ornare ligula eget, elementum metus. Praesent auctor purus nec neque molestie tincidunt. Mauris et dui aliquam, facilisis urna quis, varius ante.
Curabitur ultricies dolor in faucibus tincidunt. Fusce pellentesque lectus tempor turpis ultrices, at maximus nisi congue. Donec diam magna, lobortis eu volutpat vitae, aliquet nec quam. Suspendisse mattis felis mattis magna rutrum, eget laoreet tortor dignissim. Vivamus quis est eu arcu vehicula suscipit non vitae lacus. Cras vehicula justo vitae turpis sodales interdum. Nunc justo magna, tempus fringilla fermentum ut, sagittis in nisl. Vivamus condimentum vestibulum felis, eget hendrerit dui. Nunc molestie, neque eget consequat faucibus, magna nunc aliquam nisl, at hendrerit purus velit id nibh. Donec efficitur, nisl vel facilisis rutrum, sem est vestibulum ligula, sed suscipit magna nibh sed odio.
Integer tempus eu libero ut tincidunt. Pellentesque lobortis nunc viverra augue ornare laoreet. Donec bibendum nisl sit amet lorem tincidunt placerat. Maecenas malesuada ultrices sagittis. Donec ut tempus mi. Ut accumsan scelerisque magna. Integer ultricies cursus libero lobortis auctor. Aliquam pulvinar porttitor libero vel tempus. Sed eget fermentum dolor. Curabitur hendrerit purus vitae ipsum semper vulputate. Donec ornare id justo in ultrices. Etiam faucibus purus at lacinia blandit. Vestibulum malesuada augue at elit congue finibus. Nulla quis nunc ac metus consectetur mattis at eu dui. Phasellus mollis quam feugiat leo rutrum pellentesque.
Pellentesque elementum tortor nibh, non pellentesque augue efficitur a. Fusce id risus ac dolor pretium iaculis vitae ut risus. Vestibulum enim purus, convallis auctor enim eu, feugiat posuere orci. Sed ut finibus ligula. Nulla laoreet tortor ac tortor pellentesque aliquam. Maecenas purus magna, ullamcorper quis rutrum ultricies, tincidunt nec orci. Phasellus eu quam nec eros lacinia posuere. Vestibulum id vestibulum turpis. Proin aliquam magna odio, feugiat eleifend felis vestibulum ut. Integer pulvinar id lectus in pharetra. Aenean cursus mollis nisi, id ultricies ligula luctus ac. Integer et elit condimentum mauris vestibulum sagittis. ',

    'object1' => new DateTime('Mon, 15 Aug 2005 15:52:01 +0000'),
    'object2' => function ($a)
    {
        return;
    },
    'another entry',
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
