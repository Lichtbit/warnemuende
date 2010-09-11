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

class Page extends Model {

    public function configure() {
        $this->addIntegerField("id", 22, true, array("myType" => "apfel"));
        $this->addTextField("slug", 30);
        $this->addTextField("content", -1);
        $this->addAssociations("tag", "Tag");

        $this->setPrimaryKey("id");
        $this->addIndex("slug");
    }
}

class Tag extends Model {

    public function configure() {
        $this->addIntegerField("id", 22, true);
        $this->addTextField("tag", 30);
        $this->addIntegerField("level", 10);
        // FIXME If an integer has option auto increment true it must be a key
        $this->setPrimaryKey("id");
    }
}

$p = new Page();

//$p->createTables();
echo $p->getCreateTableStatement()."\n\n";

$t = new Tag();
//$t->createTables();
echo $t->getCreateTableStatement();

$p->setField("slug", "home");
$p->setField("content", "Hallo und herzlich willkomennßäüö");
$p->save();

$t->setField("tag", "neuerTag");
$t->save();
echo mysql_error();

//$p->dropTable();
//$t->dropTable();
die("\n--------------\nLäuft durch");

?>
