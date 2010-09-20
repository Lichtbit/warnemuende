<?php
require_once "init.php";

use warnemuende\model\mysql\Model;

$p = new Page();
$t = new Tag();
$a = new Author();

try {
$p->createTables();
$t->createTables();
$a->createTables();
} catch (Exception $e) {};

$t->setField("level", 2);
$t->setField("tag", "mein tag");
$t->save();

$a->setField("name", "Meier");
$a->setField("firstname", "Klaus");
$a->setField("age", 25);

$p->setField("slug", "home");
$p->setField("content", "Hallo und herzlich willkomennßäüö\n<br>\n--'''\\");
$p->setField("author", $a);
$p->setField("tag", array($t));
$p->save();

$n = Page::getById(array(1));
//$n = Page::getByQuery("");
echo $n->getField("content");
$neu = $n->getField("author");
echo "\n\n";
echo $neu->getField("firstname")." ".$neu->getField("name").", ".$neu->getField("age");

$tags = $n->getField("tag");
echo "\n\n";
echo $tags[0]->getField("tag").", level ".$tags[0]->getField("level");
echo "\n\n";

$m = Tag::getById(array("mein tag", 2));

$t->setField("tag", "neuerTag");
$t->save();

$p->dropTable();
$t->dropTable();
$a->dropTable();
die("\n--------------\nLäuft durch");

?>
