<?php

use PHPUnit\Framework\TestCase;
use XMLRestore\XMLRestore;
use XMLRestore\XMLTable;
use XMLRestore\XMLTableRow;

$deps = [ 'classes/XMLRestore.class.php'
        , 'classes/XMLTable.class.php'
        , 'classes/XMLTableRow.class.php'
        ];

foreach($deps as $dep) {
    require_once($dep);
}

class XMLTableRowTest extends TestCase
{

    /**
     * @dataProvider providertestGetTableRows
     * */

    function testGetTableRows($Table,$expected) {
        die(__FILE__  . ':' . __LINE__ );
        $XMLTable = new XMLTable($Table);
        $this->assertCount($XMLTable->tableRows, $expected);
    }

    function providertestGetTableRows() {

        $xml = simplexml_load_file('tests/authtest.xml');
        $buffer  = [];

        foreach($xml->database[0] as $table) {
            array_push($buffer, [$table => count($table->row)]);
        }

        return $buffer;

    }
}

