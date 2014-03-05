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
		'join' => array(),
		'where' => array(),
		'order' => '',
		'group' => ''
	);

	public function __construct($source) {
		$this->source = $source;
		$this->table = ($source['table'] != '' ? $source['table'] : '');
	}

	/**
	 * Build select clause with fields to return
	 * 
	 * @access public
	 * @param {string} $fields Fields of resultset
	 * @return instance
	 */
	public function select($fields = '*') {
		$sql = 'SELECT '.$fields;
		$this->parts['select'] = $sql;
		$this->from();
		return $this;
	}

	/**
	 * Build from clause of query
	 *
	 * @access public
	 * @param {string} $table Optional name of table
	 * @return instance
	 */
	public function from($table = null) {
		if ($table !== null) $this->table = $table;
		$sql = ' FROM '.$this->table.' '.strtoupper($this->table);
		$this->parts['from'] = $sql;

		return $this;
	}

	/**
	 * Add where clause with conditions to query
	 *
	 * @access public
	 * @param {string} $condition Part of condition to query
	 * @param {array} $params Parameters to replace of clause
	 * @return instance
	 */
	public function where($condition, $params = array()) {
		if (count($params) > 0) $this->replaceFields($condition, $params);
		$sql = (count($this->parts['where']) > 0 ? ' AND ' : ' WHERE ').'('.$condition.')';
		$this->parts['where'][] = $sql;
		return $this;
	}

	/**
	 * Add an OR conditions to where clause of query
	 *
	 * @access public
	 * @param {string} $condition Part of condition to query
	 * @param {array} $params Parameters to replace of clause
	 * @return instance
	 */
	public function orWhere($condition, $params = array()) {
		if (count($params) > 0) $this->replaceFields($condition, $params);
		$sql = (count($this->parts['where']) > 0 ? ' OR ' : ' WHERE ').'('.$condition.')';
		return $this;
	}

	/**
	 * Add a join table to query, setting by default a INNER JOIN
	 *
	 * @access public
	 * @param {string} $table Name of table to be joined
	 * @param {string} $on Condition of join table
	 * @param {string} $type Set the join type, can be [INNER, OUTER, CROSS, LEFT, RIGHT]
	 * @return instance
	 */
	public function join($table, $on, $type = 'inner') {
		if (is_array($table)) list($alias, $table) = $table;
		else {
			if (strpos($table, ' ') !== false) list($table, $alias) = explode(' ', $table);
			else $alias = $table;
		}
		$joinStr = ' '.strtoupper($type).' JOIN '.$table.' '.$alias.' ON '.$on;
		$this->parts['join'][] = $joinStr;
		return $this;
	}

	/**
	 * Add an order to query
	 *
	 * @access public 
	 * @param {string} $order Fields to be ordered in the query
	 * @return instance
	 */
	public function order($order) {
		$this->parts['order'] = ' ORDER '.$order;
		return $this;
	}

	/**
	 * Add a group to query
	 * 
	 * @access public
	 * @param {string} $group Fields to group clause of query
	 * @return instance
	 */
	public function group($group) {
		$this->parts['group'] = ' GROUP BY '.$group;
		return $this;
	}

	/**
	 * Get a builded query to execute at connector
	 *
	 * @access public
	 * @return {string} $sql
	 */
	public function getSql() {
		$this->joinAll();
		return $this->sql;
	}

	/**
	 * Set quotes to provent sql injection
	 *
	 * @access protected
	 * @param {string} $value Value to be formated
	 * @return string
	 */
	protected function quote($value) {
		if (is_int($value)) return $value;
		else if (is_float($value)) return sprintf('%F', $value);

		return "'".addcslashes($value, "\000\n\r\\'\"\032")."'";
	}

	/**
	 * Method to implode all parts of query
	 *
	 * @access public
	 * @return instance
	 */
	public function joinAll() {
		$sql = $this->implode_r('', $this->parts);
		$this->sql = $sql;
		return $this;
	}

	/**
	 * Method to replace values to '?' at conditions strings
	 *
	 * @access private
	 * @param {string} $conditions Passed by reference, conditions with '?' will be replaced by value
	 * @param {array} $params Is the value(s) to be replaced
	 * @return string $condition
	 */
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

	/**
	 * Method to implode recursively
	 *
	 * @access private
	 * @param {string} $glue Delimiter to glue for
	 * @param {array} $array to implode
	 * @return string
	 */ 
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