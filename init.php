<?php
error_reporting(E_ALL);

// Load all classes from namespace-like directories
function __autoload($className) {
    if (is_file(str_replace('\\', '/', $className).".php")) {
        require_once str_replace('\\', '/', $className).".php";
        return true;
    } else {
        return false;
    }
}

mysql_connect("localhost", "modeltester", "test");
mysql_select_db("modeltester");
mysql_query("SET NAMES 'utf8';");
mysql_query("SET CHARACTER NAMES 'utf8';");

require_once "pages/model.php";
?>
