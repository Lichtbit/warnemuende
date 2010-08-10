<?php
namespace warnemuende\model;
/**
 * Interface for preparing databases
 *
 * @author Sebastian Gaul <sebastian@mgvmedia.com>
 */
interface Initialization {

    /**
     * Sets table name for current Model
     *
     * @param string table name
     */
    public function setTableName($name);

    /**
     * Sets a field and options
     *
     * @param string $name field name
     * @param mixed[] $options
     */
    public function setField($name, $options);

    /**
     * Adds table to database
     */
    public function init();


}
?>
