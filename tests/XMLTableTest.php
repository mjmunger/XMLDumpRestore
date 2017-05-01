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

class XMLTableTest extends TestCase
{

    /**
     * @dataProvider providerTestParseTableName
     * */

    function testParseTableName($object,$expected) {
        $XMLTable = new XMLTable($object);
        $this->assertSame( $expected,$XMLTable->parseTableName() );
    }

    function providerTestParseTableName() {

        $xml = simplexml_load_file('tests/authtest.xml');
        $buffer  = [];

        foreach($xml->database[0] as $table) {
            foreach($table->attributes() as $name => $value) {
                if($name == 'name') {
                    array_push($buffer, [$table, (string) $value]);
                }
            }
        }

        return $buffer;

    }

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