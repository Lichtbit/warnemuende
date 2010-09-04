<?php
namespace warnemuende\model\mysql;

require_once "MySqlStorage.php";

/**
 * Offers functionality to load objects from databases
 *
 * @author Sebastian Gaul <sebastian@mgvmedia.com>
 */
abstract class MySqlSelection extends MySqlStorage {


    public static function getByFilter($filter, $limit = null) {
        $q  = "SELECT `".$this->getTableName()."`.* FROM `".$this->getTableName()."`\n";
        $q .= "WHERE ".self::filterToSql($filter);
        if (isset($limit)) {
            $q .= "LIMIT ".$limit."\n";
        }
        $q .= ";";

        $ms = array();
        $className = get_called_class();
        $result = mysql_query($q);
        while ($row = mysql_fetch_assoc($result)) {
            $m = new $className;
            foreach ($row as $name => $value) {
                $m->setField($name, $value);
            }
            $ms[] = $m;
        }
        return $ms;

    }
}
?>
