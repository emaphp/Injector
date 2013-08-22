<?php
//call composer autoloader
$loader = require __DIR__ . "/../vendor/autoload.php";

//set namespace dir
$loader->add('Injector\\', __DIR__ . '/../src/');

//add test classes namespace
$loader->add('Acme\\', __DIR__ . '/classes');