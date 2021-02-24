<?php

/**
 * YAWIK
 * Module Configuration
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

$modules = [
    'SlmQueue',
    'Core',
    'Auth',
    'Cv',
    'Applications',
    'Jobs',
    'Settings',
    'Pdf',
    'Geo',
    'Organizations',
    'ReleaseTools',
    'Yawik\\Migration'
];

// add ability to load additional module in autoload/*.module.php
use Symfony\Component\Finder\Finder;
$finder = Finder::create()
    ->name('*.module.php')
    ->in(__DIR__.'/autoload');

foreach($finder->files() as $file){
    $modules = array_merge($modules, include $file);
}

return $modules;