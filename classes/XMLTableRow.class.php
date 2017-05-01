<?php

namespace XMLRestore;

// Represents a row in the Table object.

class XMLTableRow
{
    public $tableName = NULL;
    public $Row       = NULL;

    function __construct($tableName, $Row) {
        $this->tableName = $tableName;
        $this->Row       = $Row;
        $this->getColumns();
    }

    function getColumns() {
        foreach($this->Row->attributes() as $x){
            var_dump($x);
        }
    }
    
}