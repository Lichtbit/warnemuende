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
    protected static $TableName;

    /**
     * Determines which database is used
     *
     * @var string
     */
    protected static $StorageEngine = "mysql";

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

    public static function getIndices() {
        $ar = array();
        if (isset(static::$Indices) && count(static::$Indices) > 0) {
            foreach (static::$Indices as $name => $indices) {
                $ar[$name] = $indices;
            }
        }
        return $ar;
    }

    public static function getTableName() {
        if (isset(static::$TableName)) {
            return static::$TableName;
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
            if (strcmp($field, ucfirst($field)) == 0) {
                continue;
            }
            $m->setField($field, static::$$field);
        }
        foreach (self::getIndices() as $indices) {
            $m->addIndex($indices);
        }
        $m->setTableName(self::getTableName());
        $m->init();
    }
}
?>
