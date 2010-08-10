<?php
namespace warnemuende\model;
require_once 'Initialization.php';
/**
 * Object adaptor allowing to save attributes to databases
 *
 * @author Sebastian Gaul <sebastian@mgvmedia.com>
 */
class Model implements Initialization {

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
    protected static $storageEngine;

    /**
     * @see Initialization#initDatabase()
     */
    public static function initDatabase() {
        // TODO
    }
}
?>
