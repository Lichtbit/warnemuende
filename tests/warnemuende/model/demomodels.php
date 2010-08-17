<?php
namespace warnemuende\model;

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

    public static $tags = array(
        "type" => "association",
        "class" => "Page",
        "cardinality" => "n"
    );

    public static $Indices = array(
        array("slug")
    );


}

class PageTag extends Model {

    public static $tag = array(
        "type" => "text",
        "maximumLength" => 30,
        "primaryKey" => true
    );

    public static $page = array(
        "type" => "association",
        "class" => "Page",
        "cardinality" => 1,
        "primaryKey" => true
    );


}
?>
