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

use warnemuende\model\mysql\Model;

mysql_connect("localhost", "modeltester", "test");
mysql_select_db("modeltester");
mysql_query("SET NAMES 'utf8';");
mysql_query("SET CHARACTER NAMES 'utf8';");

require_once "pages/model.php";

$p = new Page();

$p->createTables();
echo mysql_error();
echo $p->getCreateTableStatement()."\n\n";

$t = new Tag();
$t->createTables();
echo $t->getCreateTableStatement();

$p->setField("slug", "home");
$p->setField("content", "Hallo und herzlich willkomennßäüö");
$p->save();

$t->setField("tag", "neuerTag");
$t->save();
echo mysql_error();

$p->dropTable();
$t->dropTable();
die("\n--------------\nLäuft durch");

?>
