<?php

session_start(); // for phpunit

/**
 * @var $loader \Composer\Autoload\ClassLoader
 */
$loader = require dirname(__DIR__) . '/vendor/autoload.php';

$loader->addPsr4('DeimosTest\\', 'tests/src/');
