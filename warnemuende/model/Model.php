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

    /**
     * Prepares database tables
     */
    public static function initDatabase() {
        // TODO
    }
}
?>
