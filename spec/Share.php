<?php
namespace GarryDzeng\Store {

  use PDO;
  use PDOException;

  abstract class Share extends \PHPUnit\DbUnit\TestCase {

    /**
     * @inheritdoc
     * @throws
     */
    protected function getConnection() {

      $database = new PDO('mysql:unix_socket=/var/run/mysql.sock', getenv('STORE.USERNAME'), getenv('STORE.PASSWORD'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
      ]);

      $database->exec(
        'CREATE DATABASE IF NOT EXISTS test;'.
        'USE test;'.
        'CREATE TABLE IF NOT EXISTS store('.
          'PK INT AUTO_INCREMENT PRIMARY KEY,'.
          '`bit` BIT(8),'.
          '`tinyint` TINYINT,'.
          '`bool` BOOL,'.
          '`smallint` SMALLINT,'.
          '`mediumint` MEDIUMINT,'.
          '`int` INT,'.
          '`bigint` BIGINT,'.
          '`decimal` DECIMAL(3,2),'.
          '`float` FLOAT(3,2),'.
          '`double` DOUBLE(3,2),'.
          '`date` DATE,'.
          '`datetime` DATETIME,'.
          '`time` TIME,'.
          '`timestamp` TIMESTAMP,'.
          '`year` YEAR,'.
          '`varbinary` VARBINARY(4),'.
          '`binary` BINARY,'.
          '`tinyblob` TINYBLOB,'.
          '`blob` BLOB,'.
          '`mediumblob` MEDIUMBLOB,'.
          '`longblob` LONGBLOB,'.
          '`tinytext` TINYTEXT,'.
          '`text` TEXT,'.
          '`mediumtext` MEDIUMTEXT,'.
          '`longtext` LONGTEXT,'.
          "`enum` ENUM('A'),".
          "`set` SET('A')".
        ');'
      );

      return $this->createDefaultDBConnection(
        $database
      );
    }

    /**
     * @inheritdoc
     * @throws PDOException
     */
    protected function getDataSet() {
      return $this->createArrayDataSet(
        [
          'store'=> [
            [
              'bit'=> 1,
              'tinyint'=> 1,
              'bool'=> true,
              'smallint'=> 1000,
              'mediumint'=> 1000,
              'bigint'=> 1000,
              'int'=> 1000,
              'decimal'=> 1.1,
              'float'=> 1.1,
              'double'=> 1.1,
              'date'=> '1970-01-01',
              'datetime'=> '1970-01-01 00:00:00',
              'time'=> '00:00:00',
              'timestamp'=> '1970-01-01 00:00:01',
              'year'=> 1970,
              'varbinary'=> "\x50\x48\x50\x07",
              'binary'=> "\x01",
              'tinyblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              'mediumblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              'longblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              'blob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              'tinytext'=> 'TEXT',
              'mediumtext'=> 'TEXT',
              'longtext'=> 'TEXT',
              'text'=> 'TEXT',
              'enum'=> 'A',
              'set'=> 'A'
            ],
            [
              'bit'=> 1,
              'tinyint'=> 1,
              'bool'=> true,
              'smallint'=> 1000,
              'mediumint'=> 1000,
              'bigint'=> 1000,
              'int'=> 1000,
              'decimal'=> 1.1,
              'float'=> 1.1,
              'double'=> 1.1,
              'date'=> '1970-01-01',
              'datetime'=> '1970-01-01 00:00:00',
              'time'=> '00:00:00',
              'timestamp'=> '1970-01-01 00:00:01',
              'year'=> 1970,
              'varbinary'=> "\x50\x48\x50\x07",
              'binary'=> "\x01",
              'tinyblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              'mediumblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              'longblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              'blob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              'tinytext'=> 'TEXT',
              'mediumtext'=> 'TEXT',
              'longtext'=> 'TEXT',
              'text'=> 'TEXT',
              'enum'=> 'A',
              'set'=> 'A'
            ],
          ]
        ]
      );
    }
  }
}