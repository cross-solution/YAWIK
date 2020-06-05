<?php

if (false === strpos($_SERVER['REQUEST_URI'], '.')
    || is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $_SERVER['SCRIPT_NAME'])
) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . "/index.php";