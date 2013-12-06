<?php

/*
 * create or delete soft-links in the public-Folder to the active Modules public-Folder
 */

// all Folder with Modules
//$moduleDir = array(__dir__ . '/../module/',  __dir__ . '/../vendor/extern/');
$moduleFolder1 = glob(__dir__ . '/../module/*/Module.php');
$moduleFolder2 = glob(__dir__ . '/../vendor/extern/*/Module.php');
$moduleFolders = array_merge($moduleFolder1, $moduleFolder2);

$publicDir = __dir__ . '/../public';


// deleting all softlinks
// es werden alle Softlinks entfernt
// es ist eine genauere spezifikation möglich,
//     siehe unten den regulären Ausdruck, das müsste dann hier schon passieren
//$subFolderSoftlinks = array('css', 'images', 'js');
$dir = new DirectoryIterator($publicDir);
echo 'unlinking Files in: ' . $publicDir . PHP_EOL;
foreach ($dir as $fileinfo) {
    if ($fileinfo->isLink()) {
        echo '> ' . basename($fileinfo->getPathName()) . $fileinfo->isDir() . PHP_EOL;
        @unlink($fileinfo->getPathName());
    }
}

// TODO: filtering the active-only-modules
// $allModules ist dann wichtig, wenn man auf dem gleichen System unterschiedliche Konfigurationen hat
$activeModules = array();
$allModules = True;
//$config = require __dir__  . '/../config/application.config.php';
//if (!empty($config)) {
//    $activeModules = $config['modules'];
//}
//var_dump ($config);
//var_dump ($activeModules);
 
// adding all Softlinks


$moduleNames = array();
    foreach ($moduleFolders as $folder) {
        $link = substr($folder, strlen(__dir__));
        if (preg_match('~^/?(.*/([^/]*))/[^/]*$~', $link, $matches)) {
            if (in_array($matches[2], $activeModules) || empty($activeModules)) {
                echo 'adding symlink ' .  $matches[2] . PHP_EOL;
                $moduleNames[] = $matches[2];
                $link = $publicDir  . '/' .$matches[2];
                $target = $matches[1] . '/public';
                @symlink($target, $link);
            }
        }
}
 
// writing the softlinks in the .gitIgnore
$gitIgnorePath = __dir__ . '/../.gitignore';
if ($gitIgnore = file_get_contents($gitIgnorePath)) {
    $ignoreList = explode(PHP_EOL, $gitIgnore);
    foreach ($moduleNames as $module) {
        $ignorePath = 'public/' . $module;
        if (!in_array($ignorePath, $ignoreList)) {
            $ignoreList[] = $ignorePath;
        }
    }
    file_put_contents($gitIgnorePath, implode(PHP_EOL, $ignoreList));
}
