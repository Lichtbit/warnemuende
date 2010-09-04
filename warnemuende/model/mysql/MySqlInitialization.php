<?php
namespace warnemuende\model\mysql;

/* FIXME Whats the problem with that:
require_once '../AbstractModel.php';
   Autoload will fix this for now... */

/**
 * Prepares MySQL databases for a specific Model
 *
 * @author Sebastian Gaul <sebastian@mgvmedia.com>
 */
abstract class MySqlInitialization extends \warnemuende\model\AbstractModel {

    private $indices;

    /*
     * MySQL datatype lengths
     */
    const TINY_TEXT = 255;
    const TEXT = 65536;
    const MEDIUM_TEXT = 16777216;
    const LONG_TEXT = 4294967296;
    
    public function  __construct() {
        parent::__construct();
        $this->indices = array();
    }

    public function createTables() {
        mysql_query($this->getCreateTableStatement());
    }

    public function setTableName($name) {
        $this->tableName = $name;
    }

    public function getIndices() {
        return $this->indices;
    }

    public function addIndex(array $fields) {
        $this->indices[] = $fields;
    }

    public function getCreateTableStatement() {
        $q  = "CREATE TABLE `".$this->getTableName()."` (\n";
        foreach ($this->fields as $name => $prop) {
            if (!isset($prop["type"])) {
                trigger_error("No type given for <em>".
                              $name."</em> in Model ".get_called_class(),
                              \E_USER_ERROR);
                exit;
            }
            switch ($prop["type"]) {
                case "integer":
                    isset($prop["maximumLength"]) ? $l = $prop["maximumLength"] : $l = 11;
                    $q .= "`".$name."` INTEGER(".$l.")".(isset($prop["unsigned"]) ? " UNSIGNED" : "");
                    $q .= " NOT NULL". (isset($prop["autoIncrement"]) ? " AUTO_INCREMENT" : "");
                    $q .= (isset($prop["primaryKey"]) ? " PRIMARY KEY" : "");
                    break;
                case "text":
                    if ($prop["maximumLength"] < 0) {
                        $q .= "`".$name."` LONGTEXT";
                    } elseif ($prop["maximumLength"] <= 100) {
                        $q .= "`".$name."` CHAR(".$prop["maximumLength"].")";
                    } elseif ($prop["maximumLength"] <= self::TINY_TEXT) {
                        $q .= "`".$name."` TINYTEXT";
                    } elseif ($prop["maximumLength"] <= self::TEXT) {
                        $q .= "`".$name."` TEXT";
                    } elseif ($prop["maximumLength"] <= self::MEDIUM_TEXT) {
                        $q .= "`".$name."` MEDIUMTEXT";
                    }
                    break;
                case "association":
                    if (isset($prop["cardinality"])
                        && $prop["cardinality"] == "1") {
                        // TODO Enable more then INT-linking
                        // Object can have more than one field as primary key
                        // with all datatypes
                        $q .= "`".$name."` INTEGER UNSIGNED";
                    // If relation is m:n the m will add the connection table:
                    } elseif(isset($prop["cardinality"])
                             && $prop["cardinality"] == "m") {
                        // TODO Create another table to realize n:m association
                    } else {
                        continue(2);
                    }
                    break;
                default:
                    trigger_error("Unknown type <em>".$prop["type"].
                                  "</em> given for <em>".$name."</em> in Model ".
                                  get_called_class(), \E_USER_ERROR);
                    exit;
            }
            $q .= ",\n";
        }
        // FIXME Doesnt work yet
        // Add indices
        foreach ($this->indices as $is) {
            $q .= "INDEX (";
            $q .= "`".implode("`, `", $is)."`";
            $q .= "),\n";
        }
        $q = substr($q, 0, -2);
        $q .= "\n) CHARACTER SET 'utf8'";
        return $q.";";
    }

    public function dropTable() {
        if (isset($this->tableName)) {
            mysql_query("DROP TABlE `".$this->tableName."`");
        }
    }
}
?>
