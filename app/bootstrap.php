<?php

use Nette\Debug;
use Nette\Environment;
use Nette\Application\Route;

require_once LIBS_DIR . '/Nette/Nette/loader.php';

Debug::enable();

Environment::loadConfig();

$session = Environment::getSession();
$session->setSavePath(APP_DIR . '/sessions/');
$session->setExpiration('+1 month');

$application = Environment::getApplication();
//$application->errorPresenter = 'Error';
//$application->catchExceptions = TRUE;

$router = $application->getRouter();

$router[] = new Route('index.php', array(
	'presenter' => 'Marks',
	'action' => 'default',
), Route::ONE_WAY);

$router[] = new Route('<presenter>/<action>/<id>', array(
	'presenter' => 'Marks',
	'action' => 'default',
	'id' => NULL,
));

Debug::$maxLen = 500;

Nette\Templates\FormMacros::register();

$application->run();
