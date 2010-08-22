<?php
namespace warnemuende\model;

/**
 * Interface to store models
 *
 * @author Sebastian Gaul <sebastian@mgvmedia.com>
 */
interface Storage {

    /**
     * Sets a new value for a specified field
     *
     * @param string $name
     * @param mixed $value
     */
    public function setField($name, $value);

    /**
     * Returns saved value of a specific field
     *
     * @param string $name  Field's name
     */
    public function getField($name);

    /**
     * Saves current Model object to database
     *
     * Sets ID automatically if object was inserted
     */
    public function save();

    /**
     * Deletes current Model object from Database
     */
    public function delete();

    /**
     * Sets table name which is used to save in or delete from
     */
    public function setTableName($name);
}
?>
