<?php

namespace XMLRestore;

class XMLTable
{
    public $XMLDoc     = NULL;
    public $tableName    = NULL;
    public $PDO          = NULL;
    public $tableColumns = [];
    public $tableRows    = [];

    function __construct($tableName, $XMLDoc, $PDO) {
        $this->tableName = $tableName;
        $this->XMLDoc = $XMLDoc;
        $this->PDO = $PDO;
        $this->getTableRows();
    }

    function getColumns() {
        $columns = [];

        //Get the first row. Use clone so we don't break the original
        $tmpDoc = clone $this->XMLDoc;

        foreach($tmpDoc->getElementsByTagName('table_data') as $table) {
            if($table->getAttribute('name') != $this->tableName) continue;
            $Rows = $table->getElementsByTagName("row");
            break;
        }
        $Row = $Rows->item(0);

        if($Row === NULL) {
            echo $this->tableName . " does not have any rows to restore." . PHP_EOL;
            return false;
        }

        foreach($Row->getElementsByTagName("field") as $Field) {
            array_push($columns, (string) $Field->getAttribute("name"));
        }

        $this->tableColumns = $columns;
        return $this->tableColumns;

    }

    function getTableRows() {
        $tmpDoc = clone $this->XMLDoc;
        $tmpTable = $tmpDoc->getElementsByTagName("table_data");
        foreach($tmpTable as $table) {
            if($table->getAttribute('name') != $this->tableName) continue;

            $this->tableRows = $table->getElementsByTagName("row");
        }
    }

    //Truncate the given table.
    function truncate() {
        $sql = sprintf("TRUNCATE %s",$this->tableName);
        $stmt = $this->PDO->prepare($sql);
        $result = $stmt->execute();
        return $result;
    }

    public static function modFields($column) {
        return sprintf(" `%s`",$column);

    }

    public static function modValues($column) {
        return sprintf(" :%s",$column);

    }

    function createSQLStatement() {
        //Basic template.
        $sql = "INSERT INTO `%s` (%s) VALUES (%s)";


        //Create a re-usable column buffer:
        $columnBuffer = [];
        $columnList = $this->getColumns();

        if($columnList === false){ return false; }

        foreach($columnList as $column) {
            array_push($columnBuffer, $column);
        }

        //Turn add field names to template.
        $fieldsBuffer = [];
        foreach($this->getColumns() as $column) {
            array_push($fieldsBuffer, $column);
        }

        $fieldsBuffer = array_map(["XMLRestore\XMLTable","modFields"],$columnBuffer);
        $valueBuffer  = array_map(["XMLRestore\XMLTable","modValues"],$columnBuffer);

        $sql = sprintf( $sql
                      , $this->tableName
                      , implode(",", $fieldsBuffer)
                      , implode(",", $valueBuffer)
                      );

        return $sql;
    }

    function insertRowData() {
        $tmpDoc = clone $this->XMLDoc;
        $sql = $this->createSQLStatement();

        if($sql === false) return;
        
        $stmt = $this->PDO->prepare($sql);
        $this->PDO->beginTransaction();
 
        foreach($tmpDoc->getElementsByTagName('table_data') as $table) {
            if($table->getAttribute('name') != $this->tableName) continue;
            $Rows = $table->getElementsByTagName("row");
            break;
        }

        foreach($Rows as $Row) {
            $columns = [];
            foreach($Row->getElementsByTagName("field") as $Field) {
                $name  = $Field->getAttribute("name");
                $value = $Field->nodeValue;

                if(is_integer($value))  $value = (int) $value;
                if(strlen($value) == 0) $value = NULL;

                // printf("Name => Value : (%s) => (%s) " . PHP_EOL, $name, $value);
                $columns[$name] = $value;

            }
            //do the insert!
            $stmt->execute($columns);
        }

        $this->PDO->commit();
    }
}