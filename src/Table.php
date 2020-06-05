<?php
namespace GarryDzeng\Store {

  use InvalidArgumentException;
  use PDO;
  use PhpMyAdmin\SqlParser\Components\Condition;
  use PhpMyAdmin\SqlParser\Components\Limit;
  use PhpMyAdmin\SqlParser\Components\OrderKeyword;
  use PhpMyAdmin\SqlParser\Parser;
  use PhpMyAdmin\SqlParser\Statements\SelectStatement;
  use ReflectionClass;

  /**
   * @inheritdoc
   */
  class Table implements Contract\Table {

    private $name;
    private $definitions;
    private $parent;
    private $connection;
    private $id;

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

      $this->name = $parent ? "`$parent`.`$name`" : "`$name`";
      $this->connection = $connection;
      $this->parent = $parent;
      $this->definitions = $definitions ?? [];
      $this->id = $id ?? 'id';
    }

    private function tableize() {
      return strtolower(preg_replace('/(?<=\w)([A-Z])/', '_$1', (new ReflectionClass($this))->getShortName()));
    }

    private function whereSingleOrCompositeKey($value, &$composited = false) {

      $composited = is_array($value);

      if ($composited) {

        $keys = array_keys($value);

        // check if empty
        if (!$keys) {
          throw new InvalidArgumentException(
            'Key is empty,'.
            'empty array is not a valid composite key,'.
            'please check.'
          );
        }

        return implode(' AND ', array_map(
          function($value) { return "`$value`=?"; },
          $keys
        ));
      }

      return "$this->id=?";
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

      if (!$parameters) {
        return new Statement($this->connection->query($input), $this->definitions);
      }

      $statement = $this
        ->connection
        ->prepare($input);

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

      $fields = array_keys($state);

      if (!$fields) {
        throw new InvalidArgumentException(
          'State is empty,'.
          'you should pass an associative array (column -> value),'.
          'please check.'
        );
      }

      $escaped = [];
      $connection = $this->connection;
      $count = 0;

      foreach ($fields as $i => $field) {
        $escaped[] = "`$field`";
        $count++;
      }

      $statement = $connection->prepare(sprintf('INSERT INTO %s(%s)VALUES(?%s)', $this->name, implode(',', $escaped), str_repeat(',?', $count - 1)));
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

      $statement = $this
        ->connection
        ->prepare(
          "UPDATE $this->name ".
          "SET ".
            implode(',', array_map(
              function($update) { return "`$update`=?"; },
              $updates
            )).
          "WHERE ".
            $this->whereSingleOrCompositeKey(
              $identity,
              $composited
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

      // where: bind as primary key
      if (!$composited) {
        $statement->bindValue($parameter, $identity, $this->parameterize($this->id));
      }
      else {
        foreach ($identity as $ignored => $value) {
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
    public function delete($identity) {

      $statement = $this
        ->connection
        ->prepare(
          "DELETE FROM $this->name WHERE ".$this->whereSingleOrCompositeKey(
            $identity,
            $composited
          )
        );

      if (!$composited) {
        $statement->bindValue(1, $identity, $this->parameterize($this->id));
      }
      else {

        $parameter = 1;

        foreach ($identity as $ignored => $value) {
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