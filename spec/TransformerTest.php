<?php
namespace GarryDzeng\Store {

  use Generator;

  class TransformerTest extends Share {

    const RECORD_1_ASSOCIATIVE = [
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
    ];

    const RECORD_1_INDEXED = [
      1,
      '49',
      1,
      true,
      1000,
      1000,
      1000,
      1000,
      1.1,
      1.1,
      1.1,
      '1970-01-01',
      '1970-01-01 00:00:00',
      '00:00:00',
      '1970-01-01 00:00:01',
      1970,
      "\x50\x48\x50\x07",
      "\x01",
      "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
      "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
      "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
      "\xAf\xF9\x15\x6B\x6E\x2B\x4D\x1A\x9B\x28\x68\x94\xAC\x01\x79\xA9",
      'TEXT',
      'TEXT',
      'TEXT',
      'TEXT',
      'A',
      'A',
    ];

    public function testTransformWithoutAssociative() {

      $transformer = new Transformer();

      $db = $this
        ->getConnection()
        ->getConnection();

      $iterator = $transformer->transform($db->query('SELECT * FROM store WHERE PK=1 LIMIT 1'), false);

      // returned value should be an Generator instance
      $this->assertInstanceOf(
        Generator::class,
        $iterator
      );

      $this->assertSame(
        [['indexed'=> self::RECORD_1_INDEXED,'associative'=> []]],
        iterator_to_array(
          $iterator
        )
      );
    }

    public function testTransform() {

      $transformer = new Transformer();

      $db = $this
        ->getConnection()
        ->getConnection();

      $iterator = $transformer->transform($db->query('SELECT * FROM store WHERE PK=1 LIMIT 1'), true);

      // returned value should be an Generator instance
      $this->assertInstanceOf(
        Generator::class,
        $iterator
      );

      $this->assertSame(
        [['associative'=> self::RECORD_1_ASSOCIATIVE,'indexed'=> self::RECORD_1_INDEXED]],
        iterator_to_array(
          $iterator
        )
      );
    }
  }
}