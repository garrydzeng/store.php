<?php
namespace GarryDzeng\Store {

  use InvalidArgumentException;
  use PDO;
  use PDOException;
  use PhpMyAdmin\SqlParser\Components\Condition;
  use PhpMyAdmin\SqlParser\Components\Limit;
  use PhpMyAdmin\SqlParser\Components\OrderKeyword;
  use PhpMyAdmin\SqlParser\Parser;
  use PhpMyAdmin\SqlParser\Statements\SelectStatement;
  use ReflectionClass;
  use ReflectionException;

  /**
   * @inheritdoc
   */
  class Table implements Contract\Table {

    private $parent;
    private $definitions;  // column definitions
    private $name;         // its name
    private $connection;   // an openned connection
    private $id;           // PK

    public function __construct(PDO $connection, array $options = []) {

      [
        self::OPTION_DEFINITIONS => $definitions,
        self::OPTION_NAME => $name,
        self::OPTION_PARENT => $parent,
        self::OPTION_ID => $id
      ] = $options;

      // use defaults
      // guess it from shortname of this instance.
      if (!$name) {
        $name = $this->tableize();
      }

      $this->parent = $parent;
      $this->connection = $connection;
      $this->name = $parent ? "$parent.$name" : $name;
      $this->definitions = $definitions === true ? $this->createDefinitions($connection, $this->name) : $definitions ?? [];
      $this->id = $id ?? 'id';
    }

    private function tableize() {
      return strtolower(preg_replace('/(?<=\w)([A-Z])/', '_$1', (new ReflectionClass($this))->getShortName()));
    }

    private function keyof($nativeType, $length) {
      return $length == 1 && strcasecmp($nativeType, 'tiny') == 0 ? 'boolean' : strtolower($nativeType);
    }

    private function createDefinitions(PDO $connection, $table) {

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

      // retrieve metadata only
      $statement = $connection->query("SELECT * FROM $table LIMIT 0");
      $done = [];

      // handle by column
      for ($index = 0, $total = $statement->columnCount(); $index < $total; $index++) {

        [
          'name'=> $name,
          'native_type'=> $nativeType,
          'len'=> $len
        ] = $statement->getColumnMeta($index);

        // check if unsupported
        if (isset(
          $name,
          $nativeType,
          $len
        )) {
          $done[$name] = $binding[$this->keyof($nativeType, $len)] ?? PDO::PARAM_STR;
        }
        else {
          throw new InvalidArgumentException(
            'Metadata not found, '.
            'this database driver does not support PDO::getColumnMeta(int) function or '.
            'no result exists'
          );
        }
      }

      return $done;
    }

    private function composite($value) {

      $valid = [];

      if (is_array($value)) {

        $valid = array_keys($value);

        if (!$valid) {
          throw new InvalidArgumentException(
            'Key is empty,'.
            'empty array is not a valid composite key,'.
            'please check.'
          );
        }
      }

      return $valid;
    }

    /**
     * Get calculated offset of :page parameter (used to binding)
     * @param int $page
     * @param int $size
     * @return int
     */
    protected function page($page, $size) {
      return ($page-1) * $size;
    }

    protected function parameterize($name) {
      return $this->definitions[$name] ?? PDO::PARAM_STR;
    }

    protected function paginate($input, $preferNamedParameter = false) {

      // don't use strict mode
      // because it contains placeholder (named or question marker) probably.
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

      return implode(';', $statements);
    }

    protected function paginateBy($input, $indicator, $preferNamedParameter = false, $reversed = false) {

      // don't use strict mode
      // because it contains placeholder (named or question marker) probably.
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
              'it conflicts with our action,'.
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

      return implode(';', $statements);
    }

    protected function query($input, array $parameters = []) {

      $definitions = $this->definitions;
      $connection = $this->connection;

      if (!$parameters) {
        $statement = $connection->query($input);
      }
      else {

        $statement = $connection->prepare($input);

        foreach ($parameters as $parameter => $value) {
          $statement->bindValue(
            $parameter,
            $value,
            $this->parameterize(
              $parameter
            )
          );
        }

        $statement->execute();
      }

      return new Statement(
        $statement,
        $definitions
      );
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

      $fields = array_keys($state);

      if (!$fields) {
        throw new InvalidArgumentException(
          'State is empty,'.
          'you should pass an associative array (column -> value),'.
          'please check.'
        );
      }

      // escape
      $fields = array_map(
        function($you) { return "`$you`"; },
        $fields
      );

      $statement = $this->prepare(sprintf('INSERT INTO %s(%s)VALUES(?%s)', $this->name, implode(',', $fields), str_repeat(',?', count($fields)-1)));
      $parameter = 1;

      foreach ($state as $ignored => $value) {
        $statement->bind(
          $parameter++,
          $value,
          $this->parameterize(
            $ignored
          )
        );
      }

      $statement->execute();
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function update($identity, array $state) {

      $updates = array_keys($state);

      if (!$updates) {
        throw new InvalidArgumentException(
          'State is empty,'.
          'you should pass an associative array (column -> value),'.
          'please check.'
        );
      }

      $composite = $this->composite($identity);

      $statement = $this->prepare(
        "UPDATE $this->name".
        ' SET '.
          implode(',', array_map(
            function($update) { return "`$update`=?"; },
            $updates
          )).
        ' WHERE '.
        (
          !$composite ?
            "$this->id=?" :
            implode(' AND ', array_map(
              function($value) { return "`$value`=?"; },
              $composite
            ))
        )
      );

      $parameter = 1;

      foreach ($state as $ignored => $value) {
        $statement->bind(
          $parameter++,
          $value,
          $this->parameterize(
            $ignored
          )
        );
      }

      // where: bind as primary key
      if (!$composite) {
        $statement->bind($parameter++, $identity, $this->parameterize($this->name));
      }
      else {
        foreach ($identity as $ignored => $value) {
          $statement->bind(
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
    public function delete($identity) {

      $composite = $this->composite($identity);

      $statement = $this->prepare(
        "DELETE FROM $this->name WHERE ".
        (
          // make composite key to be where clause
          // with field escaping
          !$composite ?
            "$this->id=?":
            implode(' AND ', array_map(
              function($bloodborne) { return "`$bloodborne`=?"; },
              $composite
            ))
        )
      );

      // where: bind as primary key
      if (!$composite) {
        $statement->bind(1, $identity, $this->parameterize($this->name));
      }
      else {

        $parameter = 1;

        foreach ($identity as $ignored => $value) {
          $statement->bind(
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