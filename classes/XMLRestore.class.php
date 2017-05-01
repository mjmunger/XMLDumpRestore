<?php

namespace XMLRestore;

class XMLRestore
{
    public $xmlFilePath = NULL;
    public $Xml         = NULL;
    public $database    = NULL;
    public $tables      = [];

    function __construct($xmlFilePath) {
        $this->xmlFilePath = $xmlFilePath;
    }

    function getDatabase() {
        $this->Xml = simplexml_load_file($this->xmlFilePath);
        foreach($this->Xml->database[0]->attributes() as $attr => $value) {
            if($attr == 'name') {
                $this->database = (string) $value;
                return $this->database;
            }
        }
        throw new Exception("Database name attribute not found in database element!", 1);
    }

    function loadTables() {
        foreach($this->Xml->database->table_data as $table) {
            $T = new XMLTable($table);
            array_push($this->tables, $T);
        }
        return true;
    }
}