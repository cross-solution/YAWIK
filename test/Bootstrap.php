<?php

include __DIR__.'/../vendor/autoload.php';

putenv("APPLICATION_ENV=test");

use CoreTest\Bootstrap;

$config = include __DIR__.'/../config/config.php';

Bootstrap::init(!empty($config) ? $config : array());
