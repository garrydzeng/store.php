<?php
namespace GarryDzeng\Store\Contract {

  interface Statement {

    const FETCH_INDEXED = 1;
    const FETCH_ASSOCIATIVE = 2;
    const FETCH_NAMED = 3;

    public function bind($parameter, $value, $binding = null);
    public function bindByReference($parameter, &$value, $binding = null);
    public function visit($indexed = false);
    public function fetchAll($style = self::FETCH_ASSOCIATIVE);
    public function fetch($indexed = false);
    public function fetchColumn($column = 0);
    public function execute();
    public function reset();
    public function getDimension();
    public function total();
    public function paginate();
    public function pure();
  }
}