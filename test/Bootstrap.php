<?php

include __DIR__.'/../vendor/autoload.php';

use CoreTest\Bootstrap;

Bootstrap::init(!empty($testConfig) ? $testConfig : array());
