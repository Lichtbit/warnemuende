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

$t = new Tag();

try {
$p->createTables();
$t->createTables();
} catch (Exception $e) {};

$t->setField("level", 2);
$t->setField("tag", "mein tag");
$t->save();

$p->setField("slug", "home");
$p->setField("content", "Hallo und herzlich willkomennßäüö\n<br>\n--'''\\");
$p->setField("tag", $t);
$p->save();

$n = Page::getById(array(1));
//$n = Page::getByQuery("");
echo $n->getField("content");
$neu = $n->getField("tag");
echo "\n\n";
echo $neu->getField("level");

$m = Tag::getById(array("mein tag", 2));

$t->setField("tag", "neuerTag");
$t->save();

$p->dropTable();
$t->dropTable();
die("\n--------------\nLäuft durch");

?>
