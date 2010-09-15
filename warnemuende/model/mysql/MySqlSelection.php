<?php
namespace warnemuende\model\mysql;

require_once "MySqlStorage.php";

/**
 * Offers functionality to load objects from databases
 *
 * @author Sebastian Gaul <sebastian@mgvmedia.com>
 */
abstract class MySqlSelection extends MySqlStorage {

    /**
     * Return a Model by it's ID(s)
     *
     * @param mixed $id One value or an array of values
     * @return Model
     * @throws MySqlModelException
     */
    public static function getById($id) {
        $class = get_called_class();
        /* @var $c MySqlSelection */
        $c = new $class;
        $q  = "SELECT `".$c->getTableName()."`.*\nFROM `".$c->getTableName()."`\n";
        if (!is_array($id)) {
            $id = array($id);
        }
        $i = 0;
        $q .= "WHERE\n";
        if (count($c->getPrimaryKey()) != count($id)) {
            throw new MySqlModelException(
                "Invalid key for ".get_called_class().
                " (expecting array with ".implode($c->getPrimaryKey(), ", ").";".
                " found ".implode($id, ", ")." instead)",
                311
                );
        }
        foreach ($c->getPrimaryKey() as $key) {
            $q .= "  ".$c->getSqlValueAssignment($key, $id[$i])." AND \n";
            $i++;
        }
        $q = substr($q, 0, -5);
        $q .= ";";
        $result = self::getByQuery($q);
        if (is_array($result) && count($result) > 0) {
            return $result[0];
        } else {
            // TODO Throw an exception??
            return null;
        }
    }

    /**
     * Returns an Model by a specified query
     *
     * The query should start like this:
     *   select models.* from models where ...
     *
     * Replace models with your specific Model's table name. The returned value
     * is always an array, even if you specify limit 1 or something equivalent.
     *
     * @param string $query
     * @return Model[]
     * @throws MySqlModelException
     */
    public static function getByQuery($query) {
        /*$class = get_called_class();
        $c =  new $class;
        $q  = "SELECT `".$c->getTableName()."`.* FROM `".$c->getTableName()."`\n";
        $q .= "LIMIT 1;";*/

        $q = $query;

        $ms = array();
        $className = get_called_class();
        /* @var $c MySqlSelection */
        $c = new $className;
        $result = @mysql_query($q);
        if (mysql_errno() != 0) {
            throw new MySqlModelException(
                "Unable to load model from database with query\n\n". $q.
                "\n\n MySQL error ".mysql_errno().": '".
                mysql_error()."'",
                301
            );
        }
        while ($row = mysql_fetch_assoc($result)) {
            $m = new $className;
            foreach ($c->getFields() as $field) {
                if ($c->getFieldOption($field, "type") == "association") {
                    $targetName = $c->getFieldOption($field, "class");
                    /* @var $target MySqlSelection */
                    $target = new $targetName;
                    $id = array();
                    foreach ($target->getPrimaryKey() as $key) {
                        if (!isset($row[$target->getTableName()."_".$key])) {
                            continue 2;
                        }
                        $id[] = $row[$target->getTableName()."_".$key];
                    }
                    $m->setField($field, $targetName::getById($id));
                } elseif($c->getFieldOption($field, "type") == "associations") {
                } else {
                    $m->setField($field, $row[$field]);
                }
            }
            $ms[] = $m;
        }
        return $ms;
    }

}
?>
