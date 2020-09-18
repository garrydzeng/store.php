<?php
namespace GarryDzeng\Store {

  use PhpMyAdmin\SqlParser\Statements\SelectStatement;
  use PhpMyAdmin\SqlParser\Components\Condition;
  use PhpMyAdmin\SqlParser\Components\OrderKeyword;
  use PhpMyAdmin\SqlParser\Components\Limit;
  use PhpMyAdmin\SqlParser\Parser;
  use ReflectionClass;
  use InvalidArgumentException;
  use PDO;

  /**
   * @inheritdoc
   */
  class Table implements Contract\Table {

    private $definitions;
    private $name;
    private $connection;
    private $id;

    public function __construct(PDO $connection, array $options = [
      self::OPTION_DEFINITIONS => [],
      self::OPTION_ID => 'id'
    ])
    {
      $this->connection = $connection;

      [
        self::OPTION_NAME => $this->name,
        self::OPTION_DEFINITIONS => $this->definitions,
        self::OPTION_ID => $this->id
      ] = $options;

      assert(is_array($this->definitions), new InvalidArgumentException('Parameter definition list should be an array (column => type).'));

      /**
       * Append default name if it ends with dot character (prefix) or is empty string (logically)
       * use shorted name of instance
       * update property
       */
      if ('' == $this->name || '.' == substr($this->name, -1)) {
        $this->name .= $this->tableize();
      }
    }

    private function tableize() {
      return strtolower(preg_replace('/(?<=\w)([A-Z])/', '_$1', (new ReflectionClass($this))->getShortName()));
    }

    private function where(&$composited, $keys) {

      if (!$keys) {
        throw new InvalidArgumentException(
          'Invalid key found,'.
          'empty array is not a valid composite key,'.
          'please check.'
        );
      }

      $composited = is_array($keys);

      /**
       * structure [ this.id => ...] determined as composited key also.
       * create placeholder from fields,
       * such as "length=?"
       */
      if ($composited) {
        return array_reduce(array_keys($keys), fn($reduced, $key) => $reduced ? "$reduced AND \x60$key\x60=?" : "\x60$key\x60=?");
      }

      // determined as primary key (defined by option) otherwize.
      return "\x60{$this->id}\x60=?";
    }

    protected function parameterize($name) {
      return $this->definitions[$name] ?? PDO::PARAM_STR;
    }

    protected function offset($page, $size) {
      return ($page - 1) * $size;
    }

    protected function paginate($input, $preferNamedParameter = false) {

      // don't use strict mode, because it contains placeholder (named or question marker) probably.
      $statements = (new Parser($input))->statements;

      // travel list
      foreach ($statements as $statement) {

        // when SELECT found
        if ($statement instanceof SelectStatement) {

          // warning
          if ($statement->limit) {
            throw new InvalidArgumentException(
              "Paginate failed because \"$statement\" has a LIMIT clause, ".
              'it conflicts with our action,'.
              'please check.'
            );
          }

          $statement->limit = new Limit(
            $preferNamedParameter ? ':size' : '?',
            $preferNamedParameter ? ':page' : '?'
          );
        }
      }

      return implode(";\n", $statements);
    }

    protected function paginateBy($input, $indicator, $preferNamedParameter = false, $reversed = false) {

      // don't use strict mode, because it contains placeholder (named or question marker) probably.
      $statements = (new Parser($input))->statements;

      // travel list
      foreach ($statements as $statement) {

        if ($statement instanceof SelectStatement) {

          // warning
          if (
            $statement->limit ||
            $statement->order
          )
          {
            throw new InvalidArgumentException(
              "Paginate failed because \"$statement\" has LIMIT and/or ORDER BY clause, ".
              'it conflicts with our action, '.
              'please check.'
            );
          }

          $statement->limit = new Limit($preferNamedParameter ? ':size' : '?');
          $statement->order = [
            new OrderKeyword($indicator, 'ASC')
          ];

          $condition = new Condition($indicator.($reversed ? '<' : '>').($preferNamedParameter ? ':indicatable' : '?'));

          if (empty($statement->where)) {
            $statement->where = [
              $condition
            ];
          }
          else {
            $statement->where[] = new Condition('AND');
            $statement->where[] = $condition;
          }
        }
      }

      return implode(";\n", $statements);
    }

    protected function query($input, array $parameters = []) {

      $connection = $this->connection;

      if (!$parameters) {
        return new Statement($connection->query($input), $this->definitions);
      }

      $statement = $connection->prepare($input);

      // bind key/value
      foreach ($parameters as $parameter /* column/index */ => $value) {
        $statement->bindValue(
          $parameter,
          $value,
          $this->parameterize(
            $parameter
          )
        );
      }

      $statement->execute();

      return new Statement($statement, $this->definitions);
    }

    protected function prepare($input) {
      return new Statement($this->connection->prepare($input), $this->definitions);
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function lastSequenceValue($name = null) {
      return $this
        ->connection
        ->lastInsertId(
          $name
        )
      ;
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function create(array $state) {

      if (!$state) {
        throw new InvalidArgumentException(
          'State is empty,'.
          'you should pass an associative array (column -> value),'.
          'please check.'
        );
      }

      $escaped = [];
      $connection = $this->connection;
      $count = -1;

      foreach (array_keys($state) as $key) {
        $escaped[] = "\x60$key\x60";
        $count++;
      }

      $statement = $connection->prepare('INSERT INTO '.$this->name.'('.implode(',', $escaped).')VALUES(?'.str_repeat(',?', $count).')');
      $parameter = 1;

      // bind key/value
      foreach ($state as $ignored => $value) {
        $statement->bindValue(
          $parameter++,
          $value,
          $this->parameterize(
            $ignored
          )
        );
      }

      return $statement->execute();
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function update($keys, array $state) {

      if (!$state) {
        throw new InvalidArgumentException(
          'State is empty,'.
          'you should pass an associative array (column => value),'.
          'please check.'
        );
      }

      $statement = $this
        ->connection
        ->prepare(
          "UPDATE $this->name ".
          'SET '.array_reduce(array_keys($state), function($reduced, $key) { return $reduced ? "$reduced,\x60$key\x60=?" : "\x60$key\x60=?"; }).' '.
          'WHERE '.$this->where(
            $composited,
            $keys
          )
        );

      $parameter = 1;

      foreach ($state as $ignored => $value) {
        $statement->bindValue(
          $parameter++,
          $value,
          $this->parameterize(
            $ignored
          )
        );
      }

      if (!$composited) {
        $statement->bindValue($parameter, $keys, $this->parameterize($this->id));
      }
      else {
        foreach ($keys as $ignored => $value) {
          $statement->bindValue(
            $parameter++,
            $value,
            $this->parameterize(
              $ignored
            )
          );
        }
      }

      return $statement->execute();
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function delete($keys) {

      $statement = $this
        ->connection
        ->prepare(
          "DELETE FROM $this->name WHERE ".$this->where(
            $composited,
            $keys
          )
        );

      if (!$composited) {
        $statement->bindValue(1, $keys, $this->parameterize($this->id));
      }
      else {

        $parameter = 1;

        foreach ($keys as $ignored => $value) {
          $statement->bindValue(
            $parameter++,
            $value,
            $this->parameterize(
              $ignored
            )
          );
        }
      }

      return $statement->execute();
    }
  }
}