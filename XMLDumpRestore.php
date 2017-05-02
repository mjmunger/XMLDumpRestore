#!/usr/bin/env php
<?php

namespace XMLRestore;
use \PDO;

function my_autoloader($class) {
    $buffer = explode("\\", $class);
    $class = end($buffer);
    $candidatePath = "classes/$class.class.php";
    // echo "Looking for: $candidatePath" . PHP_EOL;
    include($candidatePath);
}

spl_autoload_register(__NAMESPACE__ . '\my_autoloader');

function show_help() {
?>

SUMMARY:

  This script reads files that were exported using mysqldump -xml -t, and then imports them into a database.

SYNTAX:

  XMLDumpRestore /path/to/file.xml

WARNING:

  This TRUNCATES your database tables! It's adviseable to BACKUP your database before doing this restore!

LIMITATIONS:
  Read the README.md file!

<?php
    exit;
}

if(count($argv) != 2) show_help();

$xmlPath = $argv[1];

if(!file_exists($xmlPath)) die("I could not file %xmlPath. Check to make sure it exists." . PHP_EOL. PHP_EOL);

if(!file_exists('database.json')) die("You must create and configure database.json so I can connect to the database." . PHP_EOL . PHP_EOL);

$options = json_decode(file_get_contents('database.json'));

$dsn = sprintf('mysql:dbname=%s;host=%s', $options->database->database, $options->database->server);

try {
    $pdo = new PDO($dsn, $options->database->username, $options->database->password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$XMLRestore = new XMLRestore($xmlPath, $pdo);
$XMLRestore->getDatabase();
$XMLRestore->loadTables();
$XMLRestore->prepareForRestore();

foreach($XMLRestore->tables as $Table) {
    printf("Restoring: %s" . PHP_EOL, $Table->tableName);
    $Table = $XMLRestore->getTable($Table->tableName);
    $Table->getTableRows();
    $Table->truncate();
    $Table->insertRowData();    
}

echo "Complete" . PHP_EOL;

