<?php

use PHPUnit\Framework\TestCase;
use XMLRestore\XMLRestore;
use XMLRestore\XMLTable;

$deps = [ 'classes/XMLRestore.class.php'
        , 'classes/XMLTable.class.php'
        ];

foreach($deps as $dep) {
    require_once($dep);
}

class XMLTableTest// **disabled because we no longer use simpleXML** extends TestCase
{


    /**
     * @dataProvider providerTestGetTableRows
     **/

    function testGetTableRows($Table,$expected) {
        $XMLTable = new XMLTable($Table);
        $XMLTable->getTableRows();
        $this->assertCount($expected, $XMLTable->tableRows);
    }

    function providerTestGetTableRows() {

        $xml = simplexml_load_file('tests/authtest.xml');
        $buffer  = [];

        foreach($xml->database[0] as $table) {
            $count = (int) count($table->row);
            array_push($buffer, [ $table, $count ]);
        }

        return $buffer;

    }

}