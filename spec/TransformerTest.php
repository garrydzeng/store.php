<?php
namespace GarryDzeng\Store {

  class TransformerTest extends Share {

    public function testTransformWithoutAssociative() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $statement = $db->query('SELECT * FROM store WHERE PK=1 LIMIT 1');

      $transformer = new Transformer();

      $this->assertSame(
        [
          [
            'associative'=> [],
            'indexed'=> [
              0 => 1,
              1 => '49',
              2 => 1,
              3 => true,
              4 => 1000,
              5 => 1000,
              6 => 1000,
              7 => 1000,
              8 => 1.1,
              9 => 1.1,
              10 => 1.1,
              11 => '1970-01-01',
              12 => '1970-01-01 00:00:00',
              13 => '00:00:00',
              14 => '1970-01-01 00:00:01',
              15 => 1970,
              16 => "\x50\x48\x50\x07",
              17 => "\x01",
              18 => "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              19 => "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              20 => "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              21 => "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              22 => 'TEXT',
              23 => 'TEXT',
              24 => 'TEXT',
              25 => 'TEXT',
              26 => 'A',
              27 => 'A',
            ]
          ]
        ],
        iterator_to_array($transformer->transform(
          $statement,
          false
        ))
      );
    }

    public function testTransform() {

      $db = $this
        ->getConnection()
        ->getConnection();

      $statement = $db->query('SELECT * FROM store WHERE PK=1 LIMIT 1');

      $transformer = new Transformer();

      $this->assertSame(
        [
          [
            'associative'=> [
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
            'indexed'=> [
              0 => 1,
              1 => '49',
              2 => 1,
              3 => true,
              4 => 1000,
              5 => 1000,
              6 => 1000,
              7 => 1000,
              8 => 1.1,
              9 => 1.1,
              10 => 1.1,
              11 => '1970-01-01',
              12 => '1970-01-01 00:00:00',
              13 => '00:00:00',
              14 => '1970-01-01 00:00:01',
              15 => 1970,
              16 => "\x50\x48\x50\x07",
              17 => "\x01",
              18 => "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              19 => "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              20 => "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              21 => "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
              22 => 'TEXT',
              23 => 'TEXT',
              24 => 'TEXT',
              25 => 'TEXT',
              26 => 'A',
              27 => 'A',
            ]
          ]
        ],
        iterator_to_array($transformer->transform(
          $statement,
          true
        ))
      );
    }
  }
}