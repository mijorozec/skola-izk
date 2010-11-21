<?php

use Nette\Environment;
use Nette\Debug;

define('CLI_DIR', __DIR__);
define('APP_DIR', CLI_DIR . '/../app');
define('LIBS_DIR', CLI_DIR . '/../libs');


require LIBS_DIR . '/Nette/loader.php';

Debug::enable();
Environment::loadConfig();

require APP_DIR . '/bootstrap-doctrine.php';