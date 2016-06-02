<?php

use MttProjecteuler\Command\DbSchema;
use MttProjecteuler\Command\UserCreate;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

$console = new Application('ProjectEuler-stat Application', '0.1');
$console->getDefinition()->addOption(
    new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev')
);

/* @var \Silex\Application $app */
$console->setDispatcher($app['dispatcher']);
$console->add((new DbSchema())->setContainer($app));
$console->add((new UserCreate())->setContainer($app));

return $console;
