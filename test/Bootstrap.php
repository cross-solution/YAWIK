<?php

include __DIR__.'/../vendor/autoload.php';

use CoreTest\Bootstrap;

if (empty($testConfig)) {
}
Bootstrap::init(!empty($testConfig) ? $testConfig : array());
