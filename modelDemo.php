<?php

error_reporting(E_ALL);

// Load all classes from namespace-like directories
function __autoload($className) {
    if (is_file(str_replace('\\', '/', $className).".php")) {
        require_once str_replace('\\', '/', $className).".php";
        return true;
    } else {
        return false;
    }
}

use warnemuende\model\Model;
/* 
 * This is a demo page for testing models
 */

class Page extends Model {

    public static $id = array(
        "type" => "integer",
        "unsigned" => true,
        "autoIncrement" => true,
        "primaryKey" => true,
        "semantic" => "identifier"
    );

    public static $slug = array(
        "type" => "text",
        "maximumLength" => 30,
        "index" => true,
        "semantic" => "page slug"
    );

    public static $title = array(
        "type" => "text",
        "semantic" => "page title"
    );

    public static $content = array(
        "type" => "longtext",
        "semantic" => "page content"
    );

    /*public static $tags = array(
        "type" => "association",
        "class" => "Page",
        "cardinality" => "n"
    );*/

    public static $Indices = array(
        "slug"
    );


}

echo "getFields(): ";
print_r(Page::getFields());
echo "\n";

echo "getFieldOption('id', 'type'): ";
echo Page::getFieldOption("id", "type");
echo "\n\n";

echo "initDatabase(): ";
echo Page::initDatabase();
echo "\n";

echo "\n\n Reached end";
?>
