<?php

namespace XMLRestore;

class XMLTable
{
    public $XMLTable     = NULL;
    public $tableName    = NULL;
    public $PDO          = NULL;
    public $tableColumns = [];
    public $tableRows    = [];

    function __construct($tableName, $XMLTable, $PDO) {
        $this->tableName = $tableName;
        $this->XMLTable = $XMLTable;
        $this->PDO = $PDO;
    }

    function getColumns() {
        $columns = [];

        //Get the first row.
        $Rows = $this->XMLTable->getElementsByTagName('row');
        $Row = $Rows->item(0);

        foreach($Row->getElementsByTagName("field") as $Field) {
            array_push($columns, (string) $Field->getAttribute("name"));
        }

        $this->tableColumns = $columns;
        return $this->tableColumns;

    }

    function getTableRows() {
        foreach($this->XMLTable->row as $field) {
            array_push($this->tableRows, $field);
        }
    }

    //Truncate the given table.
    function truncate() {
        $sql = sprintf("TRUNCATE %s",$this->tableName);
        $stmt = $this->PDO->prepare($sql);
        $result = $stmt->execute($sql);
        return $result;
    }

    public static function modFields($column) {
        return sprintf(" `%s`",$column);

    }

    function createSQLStatement() {
        //Basic template.
        $sql = "INSERT INTO `%s` (%s) VALUES (%s)";

        //Turn add field names to template.
        $columnBuffer = [];
        foreach($this->getColumns() as $column) {
            array_push($columnBuffer, $column);
        }

        $columnBuffer = array_map(["XMLRestore\XMLTable","modFields"],$columnBuffer);

        $valueBuffer = [];
        for($x = 0; $x<count($columnBuffer) ; $x++) {
            array_push($valueBuffer," ?");
        }

        $sql = sprintf( $sql
                      , $this->tableName
                      , implode(",", $columnBuffer)
                      , implode(",", $valueBuffer)
                      );

        return $sql;
    }

    function insertRowData() {
        
    }
}