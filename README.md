# XMLDumpRestore
This script restores an XML dump from MySQL, which was created with the following command:

`mysqldump --xml -t -u `

This script is designed to be used with unit tests in PHPUnit where MySQL is the back-end as noted here: https://phpunit.de/manual/current/en/database.html

# Why does this script exist?

When setting up a database unit test, you need to setup fixtures which put the database in a known state. As time goes on, and requirements change or development creates improvements, it sometimes becomes necessary to re-visit the original unit tests to make changes and modifications. When this happens, it is most convenient to be ablet o load the previous dump as a starting point, make changes to that data, and then continue the development process.

As of this writing, the MySQL LOAD XML command is useful; however, it only restores a single table at a time and does not allow an entire database to be restored. This script reads the full dump and restores the database to the exact state.

# Script limitations

Because XML information DOES NOT contain column definitions, or other related "table building" information, this _only works with existing tables_. If your table structure *does not match* the structure expected by the XML, the restore will not work.