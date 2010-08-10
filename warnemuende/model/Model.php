<?php
namespace warnemuende\model;
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
    protected static $storageEngine;

}
?>
