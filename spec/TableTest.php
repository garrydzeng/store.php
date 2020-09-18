<?php
namespace GarryDzeng\Store {

  use PDO;

  class TableExports extends Table {

    /**
     * @param PDO $connection
     */
    public function __construct(PDO $connection) {
      parent::__construct(
        $connection,
        [
          self::OPTION_NAME => 'daydream.store',
          self::OPTION_ID => 'PK',
          self::OPTION_DEFINITIONS => [
            'PK'=> PDO::PARAM_INT,
            'bit'=> PDO::PARAM_STR,
            'tinyint'=> PDO::PARAM_INT,
            'bool'=> PDO::PARAM_BOOL,
            'smallint'=> PDO::PARAM_INT,
            'mediumint'=> PDO::PARAM_INT,
            'int'=> PDO::PARAM_INT,
            'bigint'=> PDO::PARAM_INT,
            'decimal'=> PDO::PARAM_STR,
            'float'=> PDO::PARAM_STR,
            'double'=> PDO::PARAM_STR,
            'date'=> PDO::PARAM_STR,
            'datetime'=> PDO::PARAM_STR,
            'time'=> PDO::PARAM_STR,
            'timestamp'=> PDO::PARAM_STR,
            'year'=> PDO::PARAM_INT,
            'varbinary'=> PDO::PARAM_STR,
            'binary'=> PDO::PARAM_STR,
            'tinyblob'=> PDO::PARAM_STR,
            'blob'=> PDO::PARAM_STR,
            'mediumblob'=> PDO::PARAM_STR,
            'longblob'=> PDO::PARAM_STR,
            'tinytext'=> PDO::PARAM_STR,
            'text'=> PDO::PARAM_STR,
            'mediumtext'=> PDO::PARAM_STR,
            'longtext'=> PDO::PARAM_STR,
            'enum'=> PDO::PARAM_STR,
            'set'=> PDO::PARAM_STR,
          ]
        ]
      );
    }

    public function proxyPaginateBy($input, $indicator, $preferNamedParameter = false, $reversed = false) {
      return $this->paginateBy(
        $input,
        $indicator,
        $preferNamedParameter,
        $reversed
      );
    }

    public function proxyPaginate($input, $preferNamedParameter = false) {
      return $this->paginate(
        $input,
        $preferNamedParameter
      );
    }
  }

  class TableTest extends Share {

    public function testPaginate() {

      $tableExports = new TableExports($this
        ->getConnection()
        ->getConnection()
      );

      $input = <<<INPUT
INSERT INTO awbuildversion(SystemInformationID,DatabaseVersion,VersionDate,ModifiedDate) VALUES(1,'9.04.10.13.00','2004-10-13 16:43:14','2004-10-13 14:43:14');
SELECT AddressLine1,AddressLine2 FROM address;
INPUT;

      $this->assertEquals(<<<EXPECTED
INSERT INTO awbuildversion(`SystemInformationID`, `DatabaseVersion`, `VersionDate`, `ModifiedDate`) VALUES (1, '9.04.10.13.00', '2004-10-13 16:43:14', '2004-10-13 14:43:14');
SELECT AddressLine1, AddressLine2 FROM address LIMIT ?, ?
EXPECTED,
        $tableExports->proxyPaginate(
          $input,
          false
        )
      );

      // with named
      $this->assertEquals(<<<EXPECTED
INSERT INTO awbuildversion(`SystemInformationID`, `DatabaseVersion`, `VersionDate`, `ModifiedDate`) VALUES (1, '9.04.10.13.00', '2004-10-13 16:43:14', '2004-10-13 14:43:14');
SELECT AddressLine1, AddressLine2 FROM address LIMIT :page, :size
EXPECTED,
        $tableExports->proxyPaginate(
          $input,
          true
        )
      );
    }

    public function testPaginateBy() {

      $tableExports = new TableExports($this
        ->getConnection()
        ->getConnection()
      );

      $input = <<<INPUT
INSERT INTO awbuildversion(SystemInformationID,DatabaseVersion,VersionDate,ModifiedDate) VALUES(1,'9.04.10.13.00','2004-10-13 16:43:14','2004-10-13 14:43:14');
SELECT AddressLine1,AddressLine2 FROM address;
INPUT;

      // no named parameter
      $this->assertEquals(<<<EXPECTED
INSERT INTO awbuildversion(`SystemInformationID`, `DatabaseVersion`, `VersionDate`, `ModifiedDate`) VALUES (1, '9.04.10.13.00', '2004-10-13 16:43:14', '2004-10-13 14:43:14');
SELECT AddressLine1, AddressLine2 FROM address WHERE ModifiedDate>? ORDER BY ModifiedDate ASC LIMIT 0, ?
EXPECTED,
        $tableExports->proxyPaginateBy(
          $input,
          'ModifiedDate',
          false,
          false
        )
      );

      // ASC
      $this->assertEquals(<<<EXPECTED
INSERT INTO awbuildversion(`SystemInformationID`, `DatabaseVersion`, `VersionDate`, `ModifiedDate`) VALUES (1, '9.04.10.13.00', '2004-10-13 16:43:14', '2004-10-13 14:43:14');
SELECT AddressLine1, AddressLine2 FROM address WHERE ModifiedDate>:indicatable ORDER BY ModifiedDate ASC LIMIT 0, :size
EXPECTED,
        $tableExports->proxyPaginateBy(
          $input,
          'ModifiedDate',
          true,
          false
        )
      );

      // DESC
      $this->assertEquals(<<<EXPECTED
INSERT INTO awbuildversion(`SystemInformationID`, `DatabaseVersion`, `VersionDate`, `ModifiedDate`) VALUES (1, '9.04.10.13.00', '2004-10-13 16:43:14', '2004-10-13 14:43:14');
SELECT AddressLine1, AddressLine2 FROM address WHERE ModifiedDate<:indicatable ORDER BY ModifiedDate ASC LIMIT 0, :size
EXPECTED,
        $tableExports->proxyPaginateBy(
          $input,
          'ModifiedDate',
          true,
          true
        )
      );
    }

    public function testCreate() {

      $connection = $this->getConnection();

      $tableExports = new TableExports($connection->getConnection());

      $tableExports->create([
        'bit'=> 2,
        'tinyint'=> 2,
        'bool'=> true,
        'smallint'=> 2000,
        'mediumint'=> 2000,
        'bigint'=> 2000,
        'int'=> 2000,
        'decimal'=> 1.2,
        'float'=> 1.2,
        'double'=> 1.2,
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
        'tinytext'=> 'TEXT2',
        'mediumtext'=> 'TEXT2',
        'longtext'=> 'TEXT2',
        'text'=> 'TEXT2',
        'enum'=> 'A',
        'set'=> 'A'
      ]);

      $PK = $tableExports->lastSequenceValue();

      $this->assertTableContains(
        [
          'PK'=> strval($PK),
          'bit'=> '50',
          'tinyint'=> '2',
          'bool'=> '1',
          'smallint'=> '2000',
          'mediumint'=> '2000',
          'bigint'=> '2000',
          'int'=> '2000',
          'decimal'=> '1.20',
          'float'=> '1.20',
          'double'=> '1.20',
          'date'=> '1970-01-01',
          'datetime'=> '1970-01-01 00:00:00',
          'time'=> '00:00:00',
          'timestamp'=> '1970-01-01 00:00:01',
          'year'=> '1970',
          'varbinary'=> "\x50\x48\x50\x07",
          'binary'=> "\x01",
          'tinyblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
          'mediumblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
          'longblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
          'blob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
          'tinytext'=> 'TEXT2',
          'mediumtext'=> 'TEXT2',
          'longtext'=> 'TEXT2',
          'text'=> 'TEXT2',
          'enum'=> 'A',
          'set'=> 'A'
        ],
        $connection->createQueryTable(null, "SELECT * FROM daydream.store")
      );
    }

    public function testUpdate() {

      $connection = $this->getConnection();

      $tableExports = new TableExports($connection->getConnection());

      $tableExports->update(1, [
        'tinyint'=> 3,
      ]);

      // change column(tinyint) value from 1 to 3
      $this->assertTableContains(
        [
          'PK'=> '1',
          'bit'=> '49',
          'tinyint'=> '3',
          'bool'=> '1',
          'smallint'=> '1000',
          'mediumint'=> '1000',
          'bigint'=> '1000',
          'int'=> '1000',
          'decimal'=> '1.10',
          'float'=> '1.10',
          'double'=> '1.10',
          'date'=> '1970-01-01',
          'datetime'=> '1970-01-01 00:00:00',
          'time'=> '00:00:00',
          'timestamp'=> '1970-01-01 00:00:01',
          'year'=> '1970',
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
        $connection->createQueryTable(null, "SELECT * FROM daydream.store")
      );

      $tableExports->update(['PK'=> 1], [
        'tinyint'=> 4,
      ]);

      $this->assertTableContains(
        [
          'PK'=> '1',
          'bit'=> '49',
          'tinyint'=> '4',
          'bool'=> '1',
          'smallint'=> '1000',
          'mediumint'=> '1000',
          'bigint'=> '1000',
          'int'=> '1000',
          'decimal'=> '1.10',
          'float'=> '1.10',
          'double'=> '1.10',
          'date'=> '1970-01-01',
          'datetime'=> '1970-01-01 00:00:00',
          'time'=> '00:00:00',
          'timestamp'=> '1970-01-01 00:00:01',
          'year'=> '1970',
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
        $connection->createQueryTable(null, "SELECT * FROM daydream.store")
      );
    }

    public function testDelete() {

      $connection = $this->getConnection();

      $tableExports = new TableExports($connection->getConnection());

      $tableExports->delete(1);
      $tableExports->delete(['PK'=> 2]);

      $this->assertTableRowCount('store', 0);
    }
  }
}