<?php

declare(strict_types=1);

// Front controller for `php -S 0.0.0.0:$PORT -t public public/index.php`.

use Crm\App;
use Crm\Config;
use Crm\CustomerRepository;
use Crm\Database;
use Crm\Http\Request;

require_once __DIR__ . "/../src/bootstrap.php";

$config = Config::fromEnvironment();
$app = new App(new CustomerRepository(Database::open($config->databasePath)));
$app->handle(Request::fromGlobals())->send();
