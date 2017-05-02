<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use XMLRestore\XMLRestore;

$deps = ['classes/XMLRestore.class.php'
        ,'classes/XMLTable.class.php'
        ,'classes/XMLTableRow.class.php'
        ];

foreach($deps as $dep) {
    require_once($dep);
}

class XMLDBTest extends TestCase
{
    use TestCaseTrait;

    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }

    public function getDataSet()
    {
        return $this->createMySQLXMLDataSet('tests/testset.xml');
    }

    public function testTruncate() {
        $XMLRestore = new XMLRestore('tests/authtest.xml', self::$pdo);
        $XMLRestore->getDatabase();
        $XMLRestore->loadTables();
        $XMLRestore->prepareForRestore();
        $table = $XMLRestore->getTable('users');
        $table->truncate();
        $XMLRestore->postRestoreOps();

        $sql = "SELECT COUNT(*) as theCount FROM phpant.users";
        $stmt = self::$pdo->prepare($sql);
        $result = $stmt->execute();
        $row = $stmt->fetchObject();

        $this->assertSame(0, (int) $row->theCount);
    }

    public function testInsertRowData() {
        $XMLRestore = new XMLRestore('tests/authtest.xml', self::$pdo);
        $XMLRestore->getDatabase();
        $XMLRestore->loadTables();
        $XMLRestore->prepareForRestore();

        $Table = $XMLRestore->getTable('users');
        $this->assertNotFalse($Table);
        $Table->getTableRows();
        $Table->truncate();

        $sql = "SELECT COUNT(*) as theCount FROM users";
        $stmt = self::$pdo->prepare($sql);
        $result = $stmt->execute();
        $row = $stmt->fetchObject();

        //Repeats the test above, but doens't hurt to check again.
        $this->assertSame(0, (int) $row->theCount);

        $Table->insertRowData();

        $sql = "SELECT COUNT(*) as theCount FROM users";
        $stmt = self::$pdo->prepare($sql);
        $result = $stmt->execute();
        $row = $stmt->fetchObject();

        //Repeats the test above, but doens't hurt to check again.
        $this->assertSame(6, (int) $row->theCount);
    }

}