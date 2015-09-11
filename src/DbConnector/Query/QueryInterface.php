<?php
namespace ActiveOracle\Query;

interface QueryInterface {

	public function select($fields = '*');

	public function from($table = null);

	public function where($condition, $param);

	public function join($table, $on, $type = 'inner');

	public function order($order);

	public function group($group);

	public function joinAll();

	public function getSql();

	public function clean();

}