<?php

use PHPUnit\Framework\TestCase;
use XMLRestore\XMLRestore;

$deps = ['classes/XMLRestore.class.php'
        ,'classes/XMLTable.class.php'
        ,'classes/XMLTableRow.class.php'
        ];

foreach($deps as $dep) {
    require_once($dep);
}

class XMLTest extends TestCase
{
    function testGetDatabase() {
        $file = 'authtest.xml';
        $XMLRestore = new XMLRestore($file);
    
        //Make sure that the we can read 
        $XMLRestore->getDatabase();
        $this->assertSame('phpant', $XMLRestore->database);
    }

    function testLoadTables() {
        $file = 'authtest.xml';
        $XMLRestore = new XMLRestore($file);
    
        //Make sure that the we can read 
        $XMLRestore->getDatabase();

        //CHeck to make sure loading the tables willbe successful.
        $result = $XMLRestore->loadTables();
        $this->assertTrue($result);
    
        //Check to make sure we have the correct table count.
        $this->assertSame(8, count($XMLRestore->tables));
    }

    function testTable() {
        
        $file = 'authtest.xml';
        $XMLRestore = new XMLRestore($file);
    
        //Make sure that the we can read 
        $name = $XMLRestore->getDatabase();
        $this->assertSame('phpant', $name);

        //CHeck to make sure loading the tables will be successful.
        $result = $XMLRestore->loadTables();
        $this->assertTrue($result);
    
        //Check to make sure we have the correct table count.
        $this->assertSame(8, count($XMLRestore->tables));
        //Pop a table object off the tables array and check it's content. 

        $table = array_shift($XMLRestore->tables);

        //Check to make sure we know the table name.
        $this->assertSame('Version', $table->tableName);

        //This table is empty, so it should not have any rows.
        $this->assertCount(0, $table->tableRows);

        //Because the Version table is empty, let's shift the next one to start comparing fields.

        $table = array_shift($XMLRestore->tables);

        var_dump($table);
        die(__FILE__  . ':' . __LINE__ );

        //Check to make sure we know the table name.
        $this->assertSame('users_roles', $table->tableName);

        //This table only has one row, so let's verify that.
        $this->assertCount(1, $table->tableRows);

        //Verify the content of the row.
        $Row = $table->getNextRow();
        $this->assertInstanceOf("XMLRestore\\XMLTableRow", $Row);

        var_dump($table);

        $this->assertSame($Row->Table->users_roles_id    , '1');
        $this->assertSame($Row->Table->users_roles_title , 'Admin');
        $this->assertSame($Row->Table->users_roles_role  , 'A');
    }
}