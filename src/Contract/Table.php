<?php
namespace GarryDzeng\Store\Contract {

  interface Table {

    const OPTION_DEFINITIONS = 'definitions';
    const OPTION_NAME = 'name';
    const OPTION_PARENT = 'parent';
    const OPTION_ID = 'id';

    public function lastSequenceValue($name = null);

    public function create(array $state);
    public function update($identity, array $state);
    public function delete($identity);
  }
}