<?php
namespace GarryDzeng\Store {

  use InvalidArgumentException;
  use PDO;

  class TableSpy extends Table {

    public function __construct(PDO $connection) {
      parent::__construct(
        $connection,
        [
          static::OPTION_ID => 'PK',
          static::OPTION_PARENT => 'test',
          static::OPTION_NAME => 'store',
          static::OPTION_DEFINITIONS => [
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

    public function PAGINATE_STYLE_QUESTION_MARKER() {
      return $this->paginate(
        "INSERT INTO awbuildversion(SystemInformationID,DatabaseVersion,VersionDate,ModifiedDate) VALUES(1,'9.04.10.13.00','2004-10-13 16:43:14','2004-10-13 14:43:14');".
        'SELECT ContactID,CreditCardID FROM contactcreditcard WHERE ContactID IN(SELECT ContactID FROM contact);'
      );
    }

    public function PAGINATE_STYLE_NAMED() {
      return $this->paginate('SELECT AddressLine1,AddressLine2 FROM address WHERE AddressID=195', true);
    }

    public function PAGINATE_BY_STYLE_QUESTION_MARKER() {
      return $this->paginateBy(
        "INSERT INTO awbuildversion(SystemInformationID,DatabaseVersion,VersionDate,ModifiedDate) VALUES(1,'9.04.10.13.00','2004-10-13 16:43:14','2004-10-13 14:43:14');".
        'SELECT AddressLine1,AddressLine2 FROM address;',
        'ModifiedDate'
      );
    }

    public function PAGINATE_BY_STYLE_NAMED() {
      return $this->paginateBy('SELECT AddressLine1,AddressLine2 FROM address WHERE AddressID=195', 'ModifiedDate', true);
    }
  }

  class TableDefinitionSpy extends Table {

    public function __construct(PDO $connection) {
      parent::__construct(
        $connection,
        [
          static::OPTION_DEFINITIONS => true,
          static::OPTION_NAME => 'store'
        ]
      );
    }

    public function expected() {
      return [
        'PK'=> $this->parameterize('PK'),
        'bit'=> $this->parameterize('bit'),
        'tinyint'=> $this->parameterize('tinyint'),
        'bool'=> $this->parameterize('bool'),
        'smallint'=> $this->parameterize('smallint'),
        'mediumint'=> $this->parameterize('mediumint'),
        'int'=> $this->parameterize('int'),
        'bigint'=> $this->parameterize('bigint'),
        'decimal'=> $this->parameterize('decimal'),
        'float'=> $this->parameterize('float'),
        'double'=> $this->parameterize('double'),
        'date'=> $this->parameterize('date'),
        'datetime'=> $this->parameterize('datetime'),
        'time'=> $this->parameterize('time'),
        'timestamp'=> $this->parameterize('timestamp'),
        'year'=> $this->parameterize('year'),
        'varbinary'=> $this->parameterize('varbinary'),
        'binary'=> $this->parameterize('binary'),
        'tinyblob'=> $this->parameterize('tinyblob'),
        'blob'=> $this->parameterize('blob'),
        'mediumblob'=> $this->parameterize('mediumblob'),
        'longblob'=> $this->parameterize('longblob'),
        'tinytext'=> $this->parameterize('tinytext'),
        'text'=> $this->parameterize('text'),
        'mediumtext'=> $this->parameterize('mediumtext'),
        'longtext'=> $this->parameterize('longtext'),
        'enum'=> $this->parameterize('enum'),
        'set'=> $this->parameterize('set'),
      ];
    }
  }


  class TableTest extends Share {

    public function testPaginate() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $spy = new TableSpy($db);

      $this->assertEquals("INSERT INTO awbuildversion(`SystemInformationID`, `DatabaseVersion`, `VersionDate`, `ModifiedDate`) VALUES (1, '9.04.10.13.00', '2004-10-13 16:43:14', '2004-10-13 14:43:14');SELECT ContactID, CreditCardID FROM contactcreditcard WHERE ContactID IN(SELECT ContactID FROM contact) LIMIT ?, ?", $spy->PAGINATE_STYLE_QUESTION_MARKER());
      $this->assertEquals('SELECT AddressLine1, AddressLine2 FROM address WHERE AddressID=195 LIMIT :page, :size', $spy->PAGINATE_STYLE_NAMED());
      $this->assertEquals("INSERT INTO awbuildversion(`SystemInformationID`, `DatabaseVersion`, `VersionDate`, `ModifiedDate`) VALUES (1, '9.04.10.13.00', '2004-10-13 16:43:14', '2004-10-13 14:43:14');SELECT AddressLine1, AddressLine2 FROM address WHERE ModifiedDate>? ORDER BY ModifiedDate ASC LIMIT 0, ?", $spy->PAGINATE_BY_STYLE_QUESTION_MARKER());
      $this->assertEquals('SELECT AddressLine1, AddressLine2 FROM address WHERE AddressID=195 AND ModifiedDate>:indicatable ORDER BY ModifiedDate ASC LIMIT 0, :size', $spy->PAGINATE_BY_STYLE_NAMED());
    }

    public function testCreateDefinition() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $target = new TableDefinitionSpy($db);

      $this->assertEquals(
        [
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
        ],
        $target->expected()
      );
    }

    public function testCreateWithEmpty() {

      $this->expectException(InvalidArgumentException::class);

      $spy = new TableSpy(
        $this
          ->getConnection()
          ->getConnection()
      );

      $spy->create([]);
    }

    public function testCreate() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $target = new TableSpy($db);
      $target->create([
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

      $PK = $target->lastSequenceValue();

      $validable = $db
        ->query(sprintf('SELECT * FROM store WHERE PK=%d', $PK))
        ->fetch(
          PDO::FETCH_ASSOC
        );

      $this->assertEquals(
        [
          'PK'=> $PK,
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
        $validable
      );

      return $PK;
    }

    public function testUpdateWithEmpty() {

      $this->expectException(InvalidArgumentException::class);

      $db = $this
        ->getConnection()
        ->getConnection();

      $target = new TableSpy($db);
      $target->update(1, []);
    }

    public function testUpdate() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $target = new TableSpy($db);

      // update by primary key
      $target->update(
        1,
        [
          'tinytext'=> 'TINYTEXT',
          'mediumtext'=> 'MEDIUMTEXT',
          'longtext'=> 'LONGTEXT',
        ]
      );

      $result = $db
        ->query('SELECT * FROM store WHERE PK=1 LIMIT 1')
        ->fetch(
          PDO::FETCH_ASSOC
        );

      $this->assertEquals(
        [
          'PK'=> 1,
          'bit'=> '49',
          'tinyint'=> '1',
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
          'tinytext'=> 'TINYTEXT',
          'mediumtext'=> 'MEDIUMTEXT',
          'longtext'=> 'LONGTEXT',
          'text'=> 'TEXT',
          'enum'=> 'A',
          'set'=> 'A'
        ],
        $result
      );

      // update by complex keys
      $target->update(
        [
          'tinytext'=> 'TINYTEXT',
          'mediumtext'=> 'MEDIUMTEXT',
          'longtext'=> 'LONGTEXT',
        ],
        [
          'tinytext'=> 'TEXT2',
          'mediumtext'=> 'TEXT2',
          'longtext'=> 'TEXT2',
        ]
      );

      $result = $db
        ->query('SELECT * FROM store WHERE PK=1 LIMIT 1')
        ->fetch(
          PDO::FETCH_ASSOC
        );

      $this->assertEquals(
        [
          'PK'=> 1,
          'bit'=> '49',
          'tinyint'=> '1',
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
          'tinytext'=> 'TEXT2',
          'mediumtext'=> 'TEXT2',
          'longtext'=> 'TEXT2',
          'text'=> 'TEXT',
          'enum'=> 'A',
          'set'=> 'A'
        ],
        $result
      );
    }

    public function testDeleteWithEmptyKey() {

      $this->expectException(InvalidArgumentException::class);

      $db = $this
        ->getConnection()
        ->getConnection();

      $target = new TableSpy($db);

      $target->delete([]);
    }

    public function testDelete() {


      $db = $this
        ->getConnection()
        ->getConnection();

      $target = new TableSpy($db);

      $target->delete(1);

      $result = $db
        ->query('SELECT * FROM store WHERE PK=1 LIMIT 1')
        ->fetch(
          PDO::FETCH_ASSOC
        );

      $this->assertSame(
        false,
        $result
      );

      $target->delete([
        'PK'=> 2
      ]);

    }
  }
}