<?php

require __DIR__ . '/../../../../../../vendor/autoload.php';

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');


$configurator = new Nette\Configurator;

//$configurator->setDebugMode(false);
//Tracy\Debugger::enable(Tracy\Debugger::PRODUCTION);

//$configurator->enableDebugger(__DIR__ . '/log');
$configurator->setTempDirectory(__DIR__ . '/temp');

$configurator->createRobotLoader()
    ->addDirectory(__DIR__ . '/..')
    ->register();
