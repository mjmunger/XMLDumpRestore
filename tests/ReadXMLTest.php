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

class mockStmt extends PDOStatement {
    public function __construct ()
    {
        //pass
    }

    public function execute($bound_input_params = NULL)
    {
        return true;
    }
}

class mockPDO extends PDO
{
    public function __construct ()
    {
        //pass
    }

    public function prepare($statement, $options = NULL)
    {
        //pass
    }

}

class XMLTest extends TestCase
{
    function testGetDatabase() {
        $file = 'authtest.xml';
        $stub = $this->createMock('mockPDO');

        $XMLRestore = new XMLRestore($file, $stub);
    
        //Make sure that the we can read 
        $XMLRestore->getDatabase();
        $this->assertSame('phpant', $XMLRestore->database);
    }

    function testLoadTables() {
        $file = 'authtest.xml';
        $stub = $this->createMock('mockPDO');

        $XMLRestore = new XMLRestore($file, $stub);
    
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
        $stub = $this->createMock('mockPDO');

        $XMLRestore = new XMLRestore($file, $stub);
    
        //Make sure that the we can read 
        $name = $XMLRestore->getDatabase();
        $this->assertSame('phpant', $name);

        //CHeck to make sure loading the tables will be successful.
        $result = $XMLRestore->loadTables();
        $this->assertTrue($result);
    
        //Check to make sure we have the correct table count.
        $this->assertSame(8, count($XMLRestore->tables));
        //Pop a table object off the tables array and check it's content. 

    }

    /**
     * @dataProvider providerTestGetTableColumns
     **/

    function testGetTable($name, $expectedColumns) {
        $file = 'authtest.xml';
        $stub = $this->createMock('mockPDO');

        $XMLRestore = new XMLRestore($file, $stub);
        $XMLRestore->getDatabase();
        $XMLRestore->loadTables();

        $table = $XMLRestore->getTable($name);

        $this->assertSame($name, $table->tableName);
    }

    /**
     * @dataProvider providerTestGetTableColumns
     **/

    function testGetTableColumns($tableName, $expectedColumns) {
        $file = 'authtest.xml';
        $stub = $this->createMock('mockPDO');

        $XMLRestore = new XMLRestore($file, $stub);
        $XMLRestore->getDatabase();
        $XMLRestore->loadTables();
        $table = $XMLRestore->getTable($tableName);
        $actualColumns = $table->getColumns();

        $this->assertEquals(count($expectedColumns), count($actualColumns));

        foreach($expectedColumns as $expectedColumn) {
            $this->assertTrue(in_array($expectedColumn, $actualColumns));
        }
    }

    function providerTestGetTableColumns() {
        return  [ ["users_roles", ["users_roles_id", "users_roles_title", "users_roles_role"] ]
                , ["users", ['users_id', 'users_email', 'users_password', 'users_first', 'users_last', 'users_setup', 'users_nonce', 'users_token', 'users_active', 'users_last_login', 'users_mobile_token', 'users_public_key', 'users_owner_id', 'users_timezone', 'users_roles_id'] ]
                ];
    }

    /**
     * @dataProvider providerTestGetTableColumns
     **/

    function testTruncateTables($tableName,$expectedColumns) {
        $file = 'authtest.xml';
        $stub = $this->createMock('mockPDO');
        $stub->method('prepare')
             ->willReturn(new mockStmt());

        $XMLRestore = new XMLRestore($file, $stub);
        $XMLRestore->getDatabase();
        $XMLRestore->loadTables();
        $table = $XMLRestore->getTable($tableName);

        $this->assertTrue($table->truncate());

    }

    /**
     * @dataProvider providerTestCreateSQLStatement
     **/

    function testCreateSQLStatement($tableName, $expectedSQL) {
        $file = 'authtest.xml';
        $stub = $this->createMock('mockPDO');
        $stub->method('prepare')
             ->willReturn(new mockStmt());

        $XMLRestore = new XMLRestore($file, $stub);
        $XMLRestore->getDatabase();
        $XMLRestore->loadTables();
        $table = $XMLRestore->getTable($tableName);

        $this->assertSame($expectedSQL, $table->createSQLStatement());
    }

    function providerTestCreateSQLStatement() {
        return  [ ['users', 'INSERT INTO `users` ( `users_id`, `users_email`, `users_password`, `users_first`, `users_last`, `users_setup`, `users_nonce`, `users_token`, `users_active`, `users_last_login`, `users_mobile_token`, `users_public_key`, `users_owner_id`, `users_timezone`, `users_roles_id`) VALUES ( :users_id, :users_email, :users_password, :users_first, :users_last, :users_setup, :users_nonce, :users_token, :users_active, :users_last_login, :users_mobile_token, :users_public_key, :users_owner_id, :users_timezone, :users_roles_id)']
                ];
    }

}