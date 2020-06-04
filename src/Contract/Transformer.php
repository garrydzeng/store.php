<?php
namespace GarryDzeng\Store\Contract {

  use PDOStatement;

  /**
   * Create a generator used to transform record one by one.
   * @package Store\Contract
   */
  interface Transformer {
    public function transform(PDOStatement $statement, $addAssociativeArray = false);
  }
}