<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use app\core\Application;

$app = new Application();

$app->run();