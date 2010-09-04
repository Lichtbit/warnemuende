<?php
namespace warnemuende\model;

/**
 * General Model functionality
 *
 * @author Sebastian Gaul <sebastian@mgvmedia.com>
 */
abstract class AbstractModel  {

    /**
     * Database table name for this model
     *
     * @var string
     */
    protected $tableName;

    /**
     * Array of name => option[]
     *
     * @var mixed[]
     */
    protected $fields;

    public function  __construct() {
        $this->configure();
    }

    /**
     * Configures Model
     */
    abstract public function configure();

    /**
     * The name of the relation used in databases
     *
     * @return string Name
     */
    public function getTableName() {
        if (isset($this->tableName)) {
            return $this->tableName;
        } else {
            if (strpos(get_called_class(), "\\") === false) {
                $name = get_called_class();
            } else {
                $name = substr(strrchr(get_called_class(),"\\"),1);
            }
            return strtolower(substr($name, 0, 1)).substr($name, 1)."s";
        }
    }

    /**
     * Sets a name used for relation in database
     *
     * @param string $tableName
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

    protected function addGenericField($name, $config, $options) {
        $this->fields[$name] = $config;
        foreach ($options as $oname => $option) {
            if (array_key_exists($oname, $this->fields[$name])) {
                trigger_error("Using an unallowed option name for field ".$name, \E_USER_ERROR);
            }
            $this->fields[$name][$oname] = $option;
        }
    }

    /**
     * Adds an integer field to the model
     *
     * @param string $name
     * @param integer $maximumLength
     * @param boolean $unsigned
     * @param boolean $autoIncrement
     * @param boolean $primaryKey
     * @param array $options
     */
    public function addInteger($name,
                               $maximumLength,
                               $unsigned = false,
                               $autoIncrement = false,
                               $primaryKey = false,
                               array $options = array()) {
        $config = array(
            "type"          => "integer",
            "maximumLength" => $maximumLength,
            "unsigned"      => $unsigned,
            "autoIncrement" => $autoIncrement,
            "primaryKey"    => $primaryKey
        );
        $this->addGenericField($name, $config, $options);
    }

    public function addText($name,
                            $maximumLength,
                            array $options = array()) {
        $config = array(
            "type"          => "text",
            "maximumLength" => $maximumLength,
        );
        $this->addGenericField($name, $config, $options);
    }

    /**
     * Returns an array of all registered fields for the model
     *
     * @return string[]
     */
    public function getFields() {
        $a = array();
        foreach ($this->fields as $name => $o) {
            $a[] = $name;
        }
        return $a;
    }

    /**
     * Returns specified option to a specified field
     *
     * @param string $fieldName
     * @param string $optionName
     * @return mixed
     */
    public function getFieldOption($fieldName, $optionName) {
        if (isset($this->fields[$fieldName][$optionName])) {
            return $this->fields[$fieldName][$optionName];
        }
        return null;
    }
}
?>
