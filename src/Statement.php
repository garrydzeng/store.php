<?php
namespace GarryDzeng\Store {

  use InvalidArgumentException;
  use PDO;
  use PDOStatement;
  use RuntimeException;

  /**
   * @inheritdoc
   */
  class Statement implements Contract\Statement {

    private $source;
    private $transformer;
    private $maps;

    /**
     * @param PDOStatement $source
     * @param Contract\Transformer $transformer
     * @param array $maps
     */
    public function __construct(PDOStatement $source, array $maps = [], Contract\Transformer $transformer = null) {
      $this->source = $source;
      $this->transformer = $transformer ?? new Transformer();
      $this->maps = $maps;
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function bind($parameter, $value, $binding = null) {

      $statement = $this->source;
      $maps = $this->maps;

      // fallback to PARAM_STR...
      if (!$binding) {
        $binding = $maps[$parameter] ?? PDO::PARAM_STR;
      }

      // a status let caller knows what is error occurred if PDO::ATTR_ERRMODE not be PDO::ERRMODE_EXCEPTION
      return $statement->bindValue(
        $parameter,
        $value,
        $binding
      );
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function bindByReference($parameter, &$value, $binding = null) {

      $statement = $this->source;
      $maps = $this->maps;

      // fallback to PARAM_STR...
      if (!$binding) {
        $binding = $maps[$parameter] ?? PDO::PARAM_STR;
      }

      // a status let caller knows what is error occurred if PDO::ATTR_ERRMODE not be PDO::ERRMODE_EXCEPTION
      return $statement->bindParam(
        $parameter,
        $value,
        $binding
      );
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function visit($indexed = false) {

      $transformer = $this->transformer;
      $source = $this->source;

      foreach ($transformer->transform($source, $indexed == false) as [
        'associative'=> $associative,
        'indexed'=> $index,
      ]) {
        yield $indexed === false ?
          $associative :
          $index
        ;
      }
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function fetchAll($style = self::FETCH_ASSOCIATIVE, $column = 0) {

      // check style constant
      if (
        $style != static::FETCH_INDEXED &&
        $style != static::FETCH_ASSOCIATIVE &&
        $style != static::FETCH_NAMED
      )
      {
        throw new InvalidArgumentException(
          'unrecognized style! '.
          'it should be one of pre-defined constant ('.
            'FECTH_INDEXED=1,'.
            'FECTH_ASSOCIATIVE=2,'.
            'FETCH_NAMED=3'.
          ')'
        );
      }

      $transformer = $this->transformer;
      $source = $this->source;

      $result = [];

      // processing rowset
      foreach ($transformer->transform($source, $style == static::FETCH_ASSOCIATIVE || $style == static::FETCH_NAMED) as [
        'associative'=> $associative,
        'indexed'=> $index,
      ])
      {
        // assoc/indexed
        if (static::FETCH_NAMED != $style) {
          $result[] = $style == static::FETCH_ASSOCIATIVE ? $associative : $index;
        }
        else {
          foreach ($associative as $key => $value) {
            $result[$key][] = $value;
          }
        }
      }

      return $result;
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function fetch($indexed = false, $column = 0) {

      $transformer = $this->transformer;
      $source = $this->source;

      // first record
      foreach ($transformer->transform($source, $indexed == false) as [
        'associative'=> $associative,
        'indexed'=> $index,
      ])
      {
        return $indexed == false ?
          $associative :
          $index
        ;
      }

      return null;
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function execute() {
      return $this->source->execute();
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function reset() {
      return $this->source->closeCursor();
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function getDimension() {
      return $this->source->columnCount();
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function total() {
      return $this->source->rowCount();
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function nextRowset() {
      return $this->source->nextRowset();
    }

    /**
     * @inheritdoc
     */
    public function pure() {
      return $this->source;
    }
  }
}