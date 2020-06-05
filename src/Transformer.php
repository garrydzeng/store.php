<?php
namespace GarryDzeng\Store {

  use InvalidArgumentException;
  use PDO;
  use PDOStatement;
  use RuntimeException;
  use Throwable;

  /**
   * @inheritdoc
   */
  class Transformer implements Contract\Transformer {

    const bindings = [
      'float'=> 'floatval',
      'newdecimal'=> 'floatval',
      'double'=> 'floatval'
    ];

    private function keyof($nativeType, $length) {
      return $length == 1 && strcasecmp($nativeType, 'tiny') == 0 ? 'boolean' : strtolower($nativeType);
    }

    /**
     * Create result from referenced array (dereference it)...
     *
     * @param array $types
     * @param array $reference
     * @param array $maps
     *
     * @throws RuntimeException
     * @return array
     */
    private function dereference(array $types, array $reference, array $maps = []) {

      $associative = [];
      $indexed = [];

      foreach ($reference as $index => $value) {

        $callback = static::bindings[$types[$index]];

        // apply customized convertor...
        if ($callback) {
          try {
            $value = call_user_func(
              $callback,
              $value
            );
          }
          catch (Throwable $error) {
            throw new RuntimeException(
              "Cast failed, error occurred when converting $index property: ".strval($error)
            );
          }
        }

        $indexed[$index] = $value;

        if ($maps) {
          $associative[$maps[$index]] = $value;
        }
      }

      return [
        'associative'=> $associative,
        'indexed'=> $indexed,
      ];
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function transform(PDOStatement $statement, $addAssociativeArray = false) {

      static $binding = [
        'tiny'=> PDO::PARAM_INT,
        'boolean'=> PDO::PARAM_BOOL,
        'short'=> PDO::PARAM_INT,
        'newdecimal'=> PDO::PARAM_STR,
        'int24'=> PDO::PARAM_INT,
        'longlong'=> PDO::PARAM_INT,
        'long'=> PDO::PARAM_INT,
        'float'=> PDO::PARAM_STR,
        'double'=> PDO::PARAM_STR,
        'year'=> PDO::PARAM_INT,
        'integer'=> PDO::PARAM_INT,
        'bit'=> PDO::PARAM_STR,
      ];

      $extras = [];
      $intermediate = [];
      $maps = [];

      for ($index = 0, $total = $statement->columnCount(); $index < $total; $index++) {

        [
          'name'=> $NAME,
          'native_type'=> $NATIVETYPE,
          'len'=> $LEN
        ] = $statement->getColumnMeta($index);

        // check if unsupported
        if (!isset(
          $NAME,
          $NATIVETYPE,
          $LEN
        ))
        {
          throw new InvalidArgumentException(
            'Metadata not found, '.
            'this database driver does not support PDO::getColumnMeta(int) function or '.
            'no result exists'
          );
        }

        $extras[] = $this->keyof(
          $NATIVETYPE,
          $LEN
        );

        // the intermediate result will overlay everytime ...
        // index starts from 1 not 0
        $statement->bindColumn($index + 1, $intermediate[$index], $binding[$extras[$index]] ?? PDO::PARAM_STR);

        // index->name
        if ($addAssociativeArray) {
          $maps[] = $NAME;
        }
      }

      // https://phpdelusions.net/pdo/fetch_modes
      while (false !== $statement->fetch(PDO::FETCH_LAZY | PDO::FETCH_BOUND)) {

        // create result with more casting ...
        yield $this->dereference(
          $extras,
          $intermediate,
          $maps
        );
      }
    }
  }
}