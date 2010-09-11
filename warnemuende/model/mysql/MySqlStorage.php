<?php
namespace warnemuende\model\mysql;

require_once "MySqlInitialization.php";

/**
 * Stores field values in objects and databases
 *
 * @author Sebastian Gaul <sebastian@mgvmedia.com>
 */
abstract class MySqlStorage extends MySqlInitialization {
    
    /**
     * Contains all fields of an object
     *
     * @var mixded[]
     */
    protected $storage;

    public function delete() {
    }

    public function save() {
        $q = "REPLACE `".$this->getTableName()."` SET\n";
        foreach ($this->storage as $name => $value) {
            if (is_string($value)) {
                $q .= "`".$name."` = ";
                $q .= "'".mysql_real_escape_string(htmlspecialchars($value))."'";
                $q .= ",\n";
            } /*elseif(static::getAttribute($name, "type") == "association") {
                if (static::getAttribute($name, "cardinality") == "1") {
                    if ($this->$name->getId() == null) {
                        trigger_error("Unable to save reference - related ".get_class($this->$name)." object has no id");
                        continue;
                    }
                    $q .= "`".$name."` = ";
                    // TODO Foreign key can be much more complicated
                    $q .= $this->$name->getId();
                    $q .= ",\n";
                } else {
                    trigger_error("Not implemented yet", \E_USER_ERROR);
                }
            }*/ else {
                $q .= "`".$name."` = ";
                $q .= $this->$name;
                $q .= ",\n";
            }
        }
        $q = substr($q, 0, -2)."\n;";
        mysql_query($q);
    }

    public function setField($name, $value) {
        $this->storage[$name] = $value;
    }

    public function getField($name) {
        if (isset($this->storage[$name])) {
            return $this->storage[$name];
        } else {
            return null;
        }
    }

    /**
     * Sets table name which is used to save in or delete from
     */
    public function setTableName($name) {
        $this->tableName = $name;
    }
    
}
?>
