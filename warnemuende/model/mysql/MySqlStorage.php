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
        if (!isset($this->storage) || count($this->storage) == 0) {
            throw new MySqlModelException(
                "Unable to save ".get_called_class()." object (no data given)",
                201
            );
        }
        $associations = array();
        $q = "REPLACE `".$this->getTableName()."` SET\n";
        foreach ($this->storage as $name => $value) {
            if ($this->getFieldOption($name, "type") == "association") {
                // TODO Is it okay to simply save here?
                if ($this->getField($name) == null OR !is_object($this->getField($name))) {
                    continue;
                }
                /* @var $o MySqlStorage */
                $o = $this->getField($name);
                $o->save();
                foreach ($o->getPrimaryKey() as $key) {
                    $q .= $o->getSqlValueAssignment($key, $o->getField($key), true).",\n";
                }
            } elseif ($this->getFieldOption($name, "type") == "associations") {
                if ($this->getField($name) == null OR (is_array($this->getField($name))
                    AND count($this->getField($name)) == 0)) {
                    continue;
                } elseif (!is_array($this->getField($name))) {
                    throw new MySqlModelException(
                        "Field for multiple associations is not an array",
                        202);
                }
                foreach ($this->getField($name) as $assField) {
                    $associations[] = array($assField, $this->getFieldOption($name, "class"));
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
        // Set auto increment fields to current database value (for inserts)
        foreach ($this->getPrimaryKey() as $pk) {
            if ($this->getFieldOption($pk, "autoIncrement") == true) {
                $this->setField($pk, mysql_insert_id());
            }
        }
        foreach ($associations as $a) {
            $this->saveAssociationObject($a[0], $a[1]);
        }
    }

    protected function saveAssociationObject($object, $className) {
        $q = "REPLACE `".$this->getTableName()."_".$object->getTableName()."` SET \n";
        foreach ($object->getPrimaryKey() as $key) {
            $q .= $object->getSqlValueAssignment($key, $object->getField($key), true).",\n";
        }
        foreach ($this->getPrimaryKey() as $key) {
            $q .= $this->getSqlValueAssignment($key, $this->getField($key), true).",\n";
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
