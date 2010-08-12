<?php
namespace warnemuende\model;
require_once 'Initialization.php';
/**
 * Object adaptor allowing to save attributes to databases
 *
 * @author Sebastian Gaul <sebastian@mgvmedia.com>
 */
class Model {

    /**
     * Database table name for this model
     *
     * @var string
     */
    protected static $tableName;

    /**
     * Determines which database is used
     *
     * @var string
     */
    protected static $storageEngine = "mysql";

    public static function getFields() {
        $ar = array();
        foreach (get_class_vars(get_called_class()) as $name => $prop) {
            $ar[] = $name;
        }
        return $ar;
    }

    public static function getFieldOption($fieldName, $option) {
        if (isset(static::$$fieldName)) {
            // PHP seems to have problems with directly accessing this array
            $a = static::$$fieldName;
            if (isset($a[$option])) {
                return $a[$option];
            }
        }
        return null;
    }

    public static function getTableName() {
        if (isset(static::$tableName)) {
            return static::$tableName;
        } else {
            return strtolower(substr(get_called_class(), 0, 1)).substr(get_called_class(), 1)."s";
        }
    }

    /**
     * Prepares database tables
     */
    public static function initDatabase() {
        $m = new MySqlInitialization();
        foreach (self::getFields() as $field) {
            if ($field == "tableName" OR $field == "storageEngine") {
                continue;
            }
            $m->setField($field, static::$$field);
        }
        $m->setTableName(self::getTableName());
        $m->init();
    }
}
?>
