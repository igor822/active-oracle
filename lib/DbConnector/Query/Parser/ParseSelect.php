<?php 
namespace DbConnector\Query\Parser;

use DbConnector\Query\Query;

class ParseSelect implements ParserInterface {

	private $patterns = array(
		'select' => '/select([a-zA-Z0-9\s\(\)](\n|.)*)from/im',
		'from' => array(
			'with_join' => '/(?:\([\s\w]+from[\s\w\=\']+\))|from([a-zA-Z0-9\s\w\,\.\=\(\)]+)(?:\bINNER|OUTER|LEFT|RIGHT|WHERE|GROUP|ORDER\b[a-zA-Z0-9\s\w\,\.\=\<\>\(\)\'\"\:])+/im',
			'without_join' => '/(?:\([\s\w]+from[\s\w\=\']+\))|from([a-zA-Z0-9\s\w\,\.\=\(\)]+)/im'
		),
		'where' => array(
			'match' => '/WHERE([a-zA-Z0-9\s\w\.\=\<\>\[\]\'\"\:]+)\b(ORDER|GROUP|HAVING|LIMIT)\b/im',
			'fetch_content' => '/WHERE([a-zA-Z0-9\s\w\.\=\<\>\[\]\'\"\:]+)ORDER|GROUP|HAVING|LIMIT/im',
			'fetch_all' => '/WHERE([a-zA-Z0-9\s\w\.\=\<\>\[\]\'\"\:]+)/im'
		)
	);

	private $parts = array();

	private $sql = '';

	private $query = null;

	public function __construct($sql = '') {
		$this->setSql($sql);
		//$this->query = new Query();
	}

	public function setSql($sql = '') {
		if ($sql != '') $this->sql = $sql;
	}

	public function getSql() {
		return $this->sql;
	}

	public function parse($clause = 'select') {
		$method = 'parse'.ucfirst($clause);
		if (method_exists($this, $method)) {
			$this->$method();
		}	
	}

	private function parseSelect() {
		if (preg_match($this->patterns['select'], $this->getSql(), $match)) {
			$this->parts['select'] = trim($match[1]);
		}
		return $this;
	}

	private function parseFrom() {
		if (preg_match($this->patterns['from']['with_join'], $this->getSql(), $match)) {
			$from = $match[1];
			if (preg_match('/INNER|OUTER|LEFT|RIGHT|WHERE|GROUP|ORDER/im', $from, $matches, PREG_OFFSET_CAPTURE))
				$this->parts['from'] = trim(substr($from, 0, $matches[0][1]));
			else {
				$this->parts['from'] = trim($match[1]);
			}
		} else {
			if (preg_match($this->patterns['from']['without_join'], $this->getSql(), $match)) {
				$this->parts['from'] = trim($match[1]);
			}
		}
	}

	private function parseWhere() {
		$sql = $this->getSql();
		if (preg_match($this->patterns['where']['match'], $sql, $match)) {
			$this->parts['where'] = trim($match[1]);
		} else if (preg_match($this->patterns['where']['fetch_all'], $sql, $match)) {
			$this->parts['where'] = trim($match[1]);
		} else {
			$this->parts['where'] = '';
		}
	}

	public function getParts($key = '') {
		if ('' !== $key) $this->parts[$key];
		return $this->parts;
	}

}