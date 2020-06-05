<?php
namespace GarryDzeng\Store {

  use Generator;

  class StatementTest extends Share {

    const fixture = [
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
    ];

    public function testVisit() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $statement = new Statement($db->query('SELECT * FROM store'));
      $result = [];

      $visitor = $statement->visit();

      // should be Generator
      $this->assertTrue($visitor instanceof Generator);

      // iterable
      foreach ($statement->visit() as $item) {
        $result[] = $item;
      }
    }

    public function testFetchAllIndexed() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $statement = new Statement($db->query('SELECT * FROM store'));

      $this->assertSame(
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
        $statement->fetchAll(Contract\Statement::FETCH_INDEXED)
      );
    }

    public function testFetchAllAssociativeArray() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $statement = new Statement($db->query('SELECT * FROM store'));

      $this->assertSame(
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
        $statement->fetchAll(Contract\Statement::FETCH_ASSOCIATIVE /*defaults*/)
      );
    }

    public function testFetchAllNamed() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $statement = new Statement($db->query('SELECT * FROM store'));

      $this->assertSame(
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
        $statement->fetchAll(Contract\Statement::FETCH_NAMED)
      );
    }

//    public function testBinding() {
//
//      $db = $this
//        ->getConnection()
//        ->getConnection();
//
//      // first we declare PK column as PDO::PARAM_INT ...
//      $statement = new Statement($db->prepare('SELECT * FROM store WHERE PK=:PK1 OR PK=:PK2'), [
//        'PK'=> PDO::PARAM_INT
//      ]);
//
//      $statement->bind('PK1', 1);
//      $statement->bind('PK2', 2);
//
//      $statement->execute();
//
//      var_dump($statement->fetchColumn());
//      var_dump($statement->fetchColumn());
////      var_dump($statement->fetchColumn());
//
//
////      $statement = $db->prepare('SELECT * FROM store WHERE PK=:PK1 OR PK=:PK2');
////      $statement->bindValue('PK1', 1, \PDO::PARAM_INT);
////      $statement->bindValue('PK2', 1, \PDO::PARAM_INT);
////      $statement->execute();
////
////      ob_start();
////
////      $statement->debugDumpParams();
////
////      $buffer = ob_get_clean();
//
//
//    }
  }
}