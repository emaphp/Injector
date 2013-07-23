<?php
//call composer autoloader
$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->add('Injector\\', __DIR__ . '/../src/');

//extra classes
require_once __DIR__ . "/classes/CustomComponent.php";
require_once __DIR__ . "/classes/TestService.php";
require_once __DIR__ . "/classes/TestContainer.php";