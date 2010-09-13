<?php
use warnemuende\model\mysql\Model;

/**
 * A simple page
 */
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
        //$this->addIntegerField("id", 22, true);
        $this->addTextField("tag", 30);
        $this->addIntegerField("level", 10);
        // FIXME If an integer has option auto increment true it must be a key
        $this->setPrimaryKey("tag", "level");
    }
}
?>
