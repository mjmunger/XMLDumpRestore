<?php

namespace XMLRestore;
use \DOMDocument;

class XMLRestore
{
    public $xmlFilePath = NULL;
    public $XmlDoc      = NULL;
    public $database    = NULL;
    public $PDO         = NULL;
    public $tables      = [];

    function __construct($xmlFilePath, $PDO) {
        $this->xmlFilePath = $xmlFilePath;
        $this->PDO = $PDO;
    }

    function getDatabase() {

        $this->XmlDoc = new DOMDocument();
        $this->XmlDoc->load($this->xmlFilePath);

        //var_dump($this->XmlDoc);

        foreach($this->XmlDoc->getElementsByTagName('database') as $Node) {
            //Get the attributes for this node.
            $this->database = (string) $Node->getAttribute("name");
            return $this->database;

        }
        throw new Exception("Database name attribute not found in database element!", 1);
    }

    function loadTables() {

        $nodes = $this->XmlDoc->getElementsByTagName("table_data");
        
        foreach($nodes as $node) {
            $name = (string) $node->getAttribute("name");
            $T = new XMLTable($name, $node, $this->PDO);
            $this->tables[$name] = $T;
        }
        return true;
    }

    function getTable($name) {
        if(!isset($this->tables[$name])) return false;
        return $this->tables[$name];
    }

    //Turn off foreign key checking so we can do some real damage.
    function prepareForRestore() {
        $sql = "SET FOREIGN_KEY_CHECKS=0";
        $stmt = $this->PDO->prepare($sql);
        return $stmt->execute();
    }

    //Turn on foreign key checks because we are not insane.
    function postRestoreOps() {
        $sql = "SET FOREIGN_KEY_CHECKS=1";
        $stmt = $this->PDO->prepare($sql);
        return $stmt->execute();
    }

}