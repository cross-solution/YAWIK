<?php

include __DIR__.'/../vendor/autoload.php';

use CoreTest\Bootstrap;

if (empty($testConfig)) {
    $testConfig = include __DIR__.'/TestConfig.php';
}
Bootstrap::init(isset($testConfig) ? $testConfig : array());
