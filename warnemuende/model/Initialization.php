<?php
namespace warnemuende\model;
/**
 * Interface for preparing databases
 *
 * @author Sebastian Gaul <sebastian@mgvmedia.com>
 */
interface Initialization {

    /**
     * Prepares database tables
     */
    public static function initDatabase();
}
?>
