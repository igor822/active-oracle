<?php
namespace DbConnector\Query;

class Query implements QueryInterface {

	private $sql = null;

	private $source = null;

	protected $table = null;

	private $from = null;

	private $parts = array(
		'select' => '',
		'from' => '',
		'where' => array(),
		'join' => array(),
		'order' => '',
		'group' => ''
	);

	public function __construct($source) {
		$this->source = $source;
		$this->table = ($source['table'] != '' ? $source['table'] : '');
	}

	public function select($fields = '*') {
		$sql = 'SELECT '.$fields;
		$this->parts['select'] = $sql;
		$this->from();
		return $this;
	}

	public function from($table = null) {
		if ($table !== null) $this->table = $table;
		$sql = ' FROM '.$this->table.' '.strtoupper($this->table);
		$this->parts['from'] = $sql;

		return $this;
	}

	public function where($condition, $params = array()) {
		if (count($params) > 0) $this->replaceFields($condition, $params);
		$sql = (count($this->parts['where']) > 0 ? ' AND ' : ' WHERE ').'('.$condition.')';
		$this->parts['where'][] = $sql;
		return $this;
	}


	public function orWhere($condition, $params = array()) {
		if (count($params) > 0) $this->replaceFields($condition, $params);
		$sql = (count($this->parts['where']) > 0 ? ' OR ' : ' WHERE ').'('.$condition.')';
		return $this;
	}

	public function join($table, $on, $type = 'inner') {
		$joinStr = strtoupper($type).' JOIN '.$table.' '.strtoupper($table).' ON '.$on;
		$this->parts['join'][] = $joinStr;
		return $this;
	}

	public function order($order) {
		$this->parts['order'] = ' ORDER '.$order;
		return $this;
	}

	public function group($group) {
		$this->parts['group'] = ' GROUP BY '.$group;
		return $this;
	}

	public function setSql($sql = null) {
		if ($sql !== null) $this->sql = $sql;
	}

	public function getSql() {
		return $this->sql;
	}

	protected function quote($value) {
		if (is_int($value)) return $value;
		else if (is_float($value)) return sprintf('%F', $value);

		return "'".addcslashes($value, "\000\n\r\\'\"\032")."'";
	}

	public function joinAll() {
		$sql = $this->implode_r('', $this->parts);
		$this->setSql($sql);
		return $this;
	}

	private function replaceFields(&$condition, $params) {
		if (strpos($condition, '?') !== false) {
			$params = array_map(array($this, 'quote'), $params);
			$match = preg_match_all('/(\?+)/', $condition, $matches);
			if ($match) {
				unset($matches[0]);
				sort($matches);
				for ($i = 0; $i < count($matches[0]); $i++) {
					$param = !empty($params[$i]) ? $params[$i] : $params[count($params) - 1];
					$offset = 1;
					$condition = preg_replace('/(\?)/', $param, $condition, 1);
				}
			}
		}
		return $condition;
	}

	private function implode_r($glue, $array) {
		$ret = '';
		foreach ($array as $item) {
			$ret .= $glue.(is_array($item) ? $this->implode_r($glue, $item) : $item);
		}
		unset($array);
		return $ret;
	}

	public function __toString() {
		$this->joinAll();
		return $this->sql;
	}

}