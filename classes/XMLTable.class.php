<?php

namespace XMLRestore;

class XMLTable
{
    public $XMLTable  = NULL;
    public $tableName = NULL;
    public $tableRows = [];

    function __construct($XMLTable) {
        $this->XMLTable = $XMLTable;
        $this->parseTableName();
        $this->getTableRows();
    }

    function parseTableName() {
        foreach($this->XMLTable->attributes() as $attr => $value) {
            if($attr === 'name') {
                $this->tableName = (string) $value;
                return $this->tableName;
            }
        }
    }

    function getTableRows() {
        foreach($this->XMLTable->row as $field) {
            array_push($this->tableRows, $field);
        }
    }

    function getNextRow() {
        $row = array_shift($this->tableRows);
        $tableRow = new XMLTableRow($this->tableName,$row);
        return $tableRow;
    }
}