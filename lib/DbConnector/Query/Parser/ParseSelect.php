<?php 
namespace DbConnector\Query\Parser;

use DbConnector\Query\Query;

class ParseSelect implements ParserInterface {

	private $patterns = array(
		'select' => '/select([a-zA-Z0-9\s\(\)](\n|.)*)from/im',
		'from' => array(
			'with_join' => '/(?:\([\s\S]+from[\s\S]+\))?\s*from((\n|.)*?)(?=INNER|OUTER|LEFT|RIGHT|WHERE|GROUP|ORDER)/im',
			'without_join' => '/(?:\([\s\S]+from[\s\S]+\))|from([a-zA-Z0-9\s\w\,\.\=\(\)]+)/im'
		),
		'where' => array(
			'match' => '/(?:\([\s\S]+WHERE[\s\S]+\))|WHERE\s*(.*?)(?:(?=(ORDER|GROUP|HAVING|LIMIT)))/im',
			'fetch_content' => '/WHERE([a-zA-Z0-9\s\w\.\=\<\>\[\]\'\"\:]+)ORDER|GROUP|HAVING|LIMIT/im',
			'fetch_all' => '/WHERE([a-zA-Z0-9\s\w\.\=\<\>\[\]\'\"\:]+)/im'
		),
		'join' => '/((?:^|)(right|inner|left|outer)\sjoin\s(.*?)on\s(.*?\s*=\s*\S+(?:\s+(?:AND|OR).*?\s*=\s*\S+)?)\s*?(?=(inner|right|where|group|order|having|limit)?))+/im',
		'group' => array(
			'match_no_end' => '/(?:\([\s\S]+group[\s\S]+\))|group\sby((\n|.)*?)(?:(?=(ORDER|LIMIT|HAVING)))/im',
			'match_end' => '/(?:\([\s\S]+group[\s\S]+\))|group\s*by(.*)/im',
			'fetch' => '/group\s*by\s(.*)/im',
		),
		'order' => array(
			'match_end' => '/(?:\([\s\S]+order[\s\S]+\))|order\sby(.*)/im',
			'match_no_end' => '/(?:\([\s\S]+order[\s\S]+\))|order\sby(.*)(?:(?=\slimit))/im'
		)
	);

	private $parts = array();

	private $sql = '';

	private $query = null;

	public function __construct($sql = '') {
		$this->setSql($sql);
	}

	public function setSql($sql = '') {
		if ($sql != '') {
			$sql = str_replace(array("\r\n", "\n", "\t"), ' ', $sql);
			$this->sql = $sql;
		}
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

	public function parseAll() {
		$clauses = array('select', 'from', 'where', 'join', 'group', 'order');
		foreach ($clauses as $clause) {
			$this->parse($clause);
		}
		return $this;
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
			if (preg_match('/INNER|OUTER|LEFT|RIGHT|WHERE|GROUP|ORDER/im', $from, $matches, PREG_OFFSET_CAPTURE)) {
				$this->parts['from'] = trim(substr($from, 0, $matches[0][1]));
			}
			else {
				$this->parts['from'] = trim($match[1]);
			}
		} else {
			if (preg_match($this->patterns['from']['without_join'], $this->getSql(), $match)) {
				var_dump($match);exit;
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

	private function parseJoin() {
		$sql = $this->getSql();
		$join = array();
		if (preg_match_all($this->patterns['join'], $sql, $match, PREG_SET_ORDER)) {
			foreach ($match as $key => $m) {
				$join[$key]['type'] = trim($m[2]);
				$join[$key]['table'] = trim($m[3]);
				$join[$key]['condition'] = trim($m[4]);
			}
		}
		$this->parts['join'] = $join;
	}


	private function parseGroup() {
		$sql = $this->getSql();
		$group = '';
		if (preg_match($this->patterns['group']['match_no_end'], $sql, $match)) {
			if (!empty($match[1])) {
				$this->parts['group'] = trim($match[1]);
				return;
			} else {
			}
			if (preg_match_all($this->patterns['group']['match_end'], $sql, $matches, PREG_SET_ORDER)) {
				$this->parts['group'] = trim($matches[1][1]);
			} else if (preg_match($this->patterns['group']['fetch'], $sql, $matches)) {
				$this->parts['group'] = $matches[1][1];
			} else {
				$this->parts['group'] = trim($match[1]);
			}
		} else {
			if (preg_match($this->patterns['group']['match_end'], $sql, $matches, PREG_OFFSET_CAPTURE)) {
				$this->parts['group'] = trim(substr($group, 0, $matches[0][1]));
			}
			if (preg_match($this->patterns['group']['fetch'], $sql, $matches)) {
				$this->parts['group'] = $matches[1];
			}
		}
	}

	private function parseOrder() {
		$sql = $this->getSql();
		$order = '';
		if (preg_match_all($this->patterns['order']['match_end'], $sql, $matches)) {
			if (empty($matches[1])) {
				preg_match($this->patterns['order']['match_no_end'], $sql, $matches);
			}
			$this->parts['order'] = trim($matches[1][0]);
		} else {
			if (preg_match($this->patterns['order']['match_no_end'], $sql, $match)) {
				$this->parts['order'] = trim($match[1]);
			}
		}
	}

	public function getParts($key = '') {
		if ($key != '') return $this->parts[$key];
		return $this->parts;
	}

}