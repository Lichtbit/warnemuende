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
            if ($this->getFieldOption($name, "type") == "association") {
                // TODO Is it okay to simply save here?
                if ($this->getField($name) == null OR !is_object($this->getField($name))) {
                    echo "abbtuch";
                    continue;
                }
                /* @var $o MySqlStorage */
                $o = $this->getField($name);
                $o->save();
                foreach ($o->getPrimaryKey() as $key) {
                    $q .= $o->getSqlValueAssignment($key, $o->getField($key), true).",\n";
                }
            } elseif (is_string($value)) {
                $q .= "`".$name."` = ";
                $q .= "'".mysql_real_escape_string(htmlspecialchars($value))."'";
                $q .= ",\n";
            } else {
                $q .= "`".$name."` = ";
                $q .= $value;
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

    protected function getSqlValueAssignment($field, $value, $asForeignKey = false) {
        $q = "`".$this->getTableName()."`.`".$field."` = ";
        if ($asForeignKey) {
            $q = "`".$this->getTableName()."_".$field."` = ";
        }
        if ($this->getFieldOption($field, "type") == "text") {
            $q .= "'".$value."'";
        } else {
            $q .= $value;
        }
        
        return $q;
    }
    
}
?>
