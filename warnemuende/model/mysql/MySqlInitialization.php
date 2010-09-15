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

    /*
     * MySQL datatype lengths
     */
    const TINY_TEXT = 255;
    const TEXT = 65536;
    const MEDIUM_TEXT = 16777216;
    const LONG_TEXT = 4294967296;
    
    public function  __construct() {
        parent::__construct();
    }

    public function createTables() {
        if ($this->tableExists($this->getTableName())) {
            throw new MySqlModelException(
                "There is already a table named ".$this->getTableName().
                " - cannot create it for Model ".get_called_class(),
                101);
        }
        $qs = explode(";\n", $this->getCreateTableStatement());
        foreach ($qs as $query) {
            mysql_query($query);
        }
    }

    public function setTableName($name) {
        $this->tableName = $name;
    }

    public function getCreateTableStatement() {
        // Used below
        $additionalTables = array();
        $q  = "CREATE TABLE `".$this->getTableName()."` (\n";
        foreach ($this->fields as $name => $prop) {
            if (!isset($prop["type"])) {
                throw new MySqlModelException(
                    "No type given for field ".
                    $name." in Model ".get_called_class(),
                    111
                );
            }
            if ($prop["type"] == "association") {
                // A simple association can be a inserted in the same table
                /* @var $c AbstractModel */
                $c = new $prop["class"];
                if (count($c->getPrimaryKey()) < 1) {
                    throw new MySqlModelException(
                        "Association targets need primary keys", 112
                    );
                }
                foreach ($c->getPrimaryKey() as $key) {
                    $q .= $c->getFieldSqlStatement($c->getTableName()."_".
                          $key, $c->getFieldOptions($key), true).",\n";
                }
                //$q = substr($q, 0, -2);
            } elseif ($prop["type"] == "associations") {
                $additionalTables[] = $this->createAssociationTable($prop["class"]);
            } else {
                $q .= $this->getFieldSqlStatement($name, $prop);
                $q .= ",\n";
            }
        }
        if (count($this->getPrimaryKey()) > 0) {
            $q .= "PRIMARY KEY (`".implode("`, `", $this->getPrimaryKey())."`),\n";
        }
        foreach ($this->getIndices() as $is) {
            $q .= "INDEX (";
            $q .= "`".implode("`, `", $is)."`";
            $q .= "),\n";
        }
        $q = substr($q, 0, -2);
        $q .= "\n) CHARACTER SET 'utf8'";
        $q .= ";";
        if (count($additionalTables) > 0) {
            $q .= "\n\n";
            $q .= implode("\n\n", $additionalTables);
        }
        return $q;
    }

    /**
     * SQL code for creating a table for associations
     *
     * @param string $className Class name of target class
     * @return string SQL code
     */
    protected function createAssociationTable($className) {
        /* @var $c \warnemuende\model\AbstractModel */
        $c = new $className;
        $q  = "CREATE TABLE `".$this->getTableName()."_".$c->getTableName()."` (\n";
        foreach ($this->getPrimaryKey() as $key) {
            $q .= $this->getFieldSqlStatement($this->getTableName()."_".$key, $this->getFieldOptions($key), true).",\n";
        }
        foreach ($c->getPrimaryKey() as $key) {
            $q .= $c->getFieldSqlStatement($c->getTableName().
                  "_".$key, $c->getFieldOptions($key), true).",\n";
        }
        if (count($this->getPrimaryKey()) > 0) {
            $q .= "PRIMARY KEY (`".$this->getTableName()
                    ."_".implode("`, `".$this->getTableName()
                    ."_", $this->getPrimaryKey())."`, `".$c->getTableName()
                    ."_".implode("`, `".$c->getTableName()
                    ."_", $c->getPrimaryKey())."`),\n";
        }
        $q = substr($q, 0, -2);
        $q .= "\n) CHARACTER SET 'utf8'";
        $q .= ";";
        return $q;

    }

    /**
     * Creates a line for a SQL creation statement
     *
     * Simply uses a name and the common config array. If the field is a foreign
     * key, i.e. must not contain auto increment and these things, set foreign
     * key parameter to true.
     *
     * @param string $name
     * @param mixed[] $config
     * @param boolean $foreignKey
     * @return string SQL statement part
     */
    protected function getFieldSqlStatement($name, $config, $foreignKey = false) {
        switch ($config["type"]) {
            case "integer":
                return $this->getIntegerSqlStatement($name, $config, $foreignKey);
                break;
            case "text":
                return $this->getTextSqlStatement($name, $config, $foreignKey);
                break;
            default:
                throw new MySqlModelException(
                    "Unknown type specified for field ".$name." in ".get_called_class(),
                    113
                );
        }
    }

    protected function getIntegerSqlStatement($name, $prop, $foreignKey = false) {
        if (isset($prop["maximumLength"]) && $prop["maximumLength"]) {
            $l = $prop["maximumLength"];
        } else {
             $l = 11;
        }
        $q = "`".$name."` INTEGER(".$l.")";
        if (isset($prop["unsigned"]) && $prop["unsigned"]) {
            $q .= " UNSIGNED";
        }
        $q .= " NOT NULL";
        if (!$foreignKey && isset($prop["autoIncrement"]) && $prop["autoIncrement"]) {
            $q .= " AUTO_INCREMENT";
        }
        return $q;
    }

    protected function getTextSqlStatement($name, $prop, $foreignKey = false) {
        if ($prop["maximumLength"] < 0) {
            $q = "`".$name."` LONGTEXT";
        } elseif ($prop["maximumLength"] <= 100) {
            $q = "`".$name."` VARCHAR(".$prop["maximumLength"].")";
        } elseif ($prop["maximumLength"] <= self::TINY_TEXT) {
            $q = "`".$name."` TINYTEXT";
        } elseif ($prop["maximumLength"] <= self::TEXT) {
            $q = "`".$name."` TEXT";
        } elseif ($prop["maximumLength"] <= self::MEDIUM_TEXT) {
            $q = "`".$name."` MEDIUMTEXT";
        }
        return $q;
    }

    public function dropTable() {
        if ($this->tableExists($this->getTableName())) {
            foreach ($this->getUsedTableNames() as $table) {
                mysql_query("DROP TABLE `".$table."`;");
            }
        } else {
            throw new MySqlModelException(
                "Unable to find table(s) (".$implode($this->getUsedTableNames(), ", ".
                ") for dropping"),
                121);
        }
    }

    private function getUsedTableNames() {
        $ar = array($this->getTableName());
        foreach ($this->getFields() as $field) {
            if ($this->getFieldOption($field, "type") == "associations") {
                $className = $this->getFieldOption($field, "class");
                $c = new $className;
                $tableName = $this->getTableName()."_".$c->getTableName();
                $ar[] = $tableName;
            }
        }
        return $ar;
    }

    public function tableExists($tableName) {
        $there = true;
        foreach ($this->getUsedTableNames() as $tableName) {
            $result = mysql_query("SHOW TABLES LIKE '".$tableName."';");
            if (mysql_num_rows($result) < 1) {
                $there = false;
            }
        }
        return $there;
    }
}
?>
