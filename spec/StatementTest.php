<?php
namespace GarryDzeng\Store {

  use Generator;
  use PDO;

  class StatementTest extends Share {

    public function testVisit() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $visitor = (new Statement($db->query('SELECT * FROM store')))->visit(false);

      // returned value should be an Generator instance
      $this->assertInstanceOf(
        Generator::class,
        $visitor
      );

      $this->assertEquals(
        [
          [
            'PK'=> 1,
            'bit'=> '49',
            'tinyint'=> 1,
            'bool'=> true,
            'smallint'=> 1000,
            'mediumint'=> 1000,
            'int'=> 1000,
            'bigint'=> 1000,
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
            'blob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
            'mediumblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
            'longblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
            'tinytext'=> 'TEXT',
            'text'=> 'TEXT',
            'mediumtext'=> 'TEXT',
            'longtext'=> 'TEXT',
            'enum'=> 'A',
            'set'=> 'A',
          ],
          [
            'PK'=> 2,
            'bit'=> '49',
            'tinyint'=> 1,
            'bool'=> true,
            'smallint'=> 1000,
            'mediumint'=> 1000,
            'int'=> 1000,
            'bigint'=> 1000,
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
            'blob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
            'mediumblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
            'longblob'=> "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
            'tinytext'=> 'TEXT',
            'text'=> 'TEXT',
            'mediumtext'=> 'TEXT',
            'longtext'=> 'TEXT',
            'enum'=> 'A',
            'set'=> 'A',
          ]
        ],
        iterator_to_array(
          $visitor
        )
      );
    }

    public function testVisitIndexed() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $visitor = (new Statement($db->query('SELECT * FROM store')))->visit(true);

      // returned value should be an Generator instance
      $this->assertInstanceOf(
        Generator::class,
        $visitor
      );

      $this->assertEquals(
        [
          [
            1, // PK
            '49', // bit
            1, // tinyint
            true, // bool
            1000, // smallint
            1000, // mediumint
            1000, // int
            1000, // bigint
            1.1, // decimal
            1.1, // float
            1.1, // double
            '1970-01-01', // date
            '1970-01-01 00:00:00', // datetime
            '00:00:00', // time
            '1970-01-01 00:00:01', // timestamp
            1970, // year
            "\x50\x48\x50\x07", // varbinary
            "\x01", // binary
            "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9", // tinyblob
            "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9", // blob
            "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9", // mediumblob
            "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9", // longblob
            'TEXT', // tinytext
            'TEXT', // text
            'TEXT', // mediumtext
            'TEXT', // longtext
            'A', // enum
            'A', // set
          ],
          [
            2, // PK
            '49', // bit
            1, // tinyint
            true, // bool
            1000, // smallint
            1000, // mediumint
            1000, // int
            1000, // bigint
            1.1, // decimal
            1.1, // float
            1.1, // double
            '1970-01-01', // date
            '1970-01-01 00:00:00', // datetime
            '00:00:00', // time
            '1970-01-01 00:00:01', // timestamp
            1970, // year
            "\x50\x48\x50\x07", // varbinary
            "\x01", // binary
            "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9", // tinyblob
            "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9", // blob
            "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9", // mediumblob
            "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9", // longblob
            'TEXT', // tinytext
            'TEXT', // text
            'TEXT', // mediumtext
            'TEXT', // longtext
            'A', // enum
            'A', // set
          ]
        ],
        iterator_to_array(
          $visitor
        )
      );
    }

    public function testFecthNamed() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $statement = new Statement($db->query('SELECT * FROM store'));

      $this->assertEquals(
        [
          'PK'=> [1,2],
          'bit'=> ['49', '49'],
          'tinyint'=> [1, 1],
          'bool'=> [true, true],
          'smallint'=> [1000, 1000],
          'mediumint'=> [1000, 1000],
          'int'=> [1000,1000],
          'bigint'=> [1000,1000],
          'decimal'=> [1.1,1.1],
          'float'=> [1.1,1.1],
          'double'=> [1.1,1.1],
          'date'=> ['1970-01-01','1970-01-01'],
          'datetime'=> ['1970-01-01 00:00:00','1970-01-01 00:00:00'],
          'time'=> ['00:00:00','00:00:00'],
          'timestamp'=> ['1970-01-01 00:00:01','1970-01-01 00:00:01'],
          'year'=> [1970,1970],
          'varbinary'=> ["\x50\x48\x50\x07","\x50\x48\x50\x07"],
          'binary'=> ["\x01","\x01"],
          'tinyblob'=> ["\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9", "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9"],
          'blob'=> ["\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9","\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9"],
          'mediumblob'=> ["\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9","\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9"],
          'longblob'=> ["\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9","\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9"],
          'tinytext'=> ['TEXT','TEXT'],
          'text'=> ['TEXT','TEXT'],
          'mediumtext'=> ['TEXT','TEXT'],
          'longtext'=> ['TEXT','TEXT'],
          'enum'=> ['A','A'],
          'set'=> ['A','A'],
        ],
        $statement->fetchAll(Statement::FETCH_NAMED)
      );
    }

    public function testBind() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $statement = new Statement($db->prepare('SELECT * FROM store WHERE `PK`=:PK AND `tinyint`=:tinyint AND `text`=:text'), [
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
      ]);

      $statement->bind('PK', 1);

      // declare as PDO::PARAM_INT when constructing class,but we overwrite to PDO::PARAM_STR here
      $statement->bind('tinyint', 2, PDO::PARAM_STR);
      $statement->bind('text', 'A');

      $pure = $statement->pure();

      ob_start();

      $pure->debugDumpParams();

      $this->assertEquals(<<<TEXT
SQL: [74] SELECT * FROM store WHERE `PK`=:PK AND `tinyint`=:tinyint AND `text`=:text
Params:  3
Key: Name: [3] :PK
paramno=-1
name=[3] ":PK"
is_param=1
param_type=1
Key: Name: [8] :tinyint
paramno=-1
name=[8] ":tinyint"
is_param=1
param_type=2
Key: Name: [5] :text
paramno=-1
name=[5] ":text"
is_param=1
param_type=2

TEXT
        , ob_get_clean());
    }
  }
}