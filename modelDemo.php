<?php

error_reporting(E_ALL);

// Load all classes from namespace-like directories
function __autoload($className) {
    require_once str_replace('\\', '/', $className).".php";
}

use warnemuende\model\Model;
/* 
 * This is a demo page for testing models
 */

$model = new Model();

echo "\n\n Reached end";
?>
