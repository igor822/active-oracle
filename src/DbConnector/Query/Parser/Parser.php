<?php 
namespace ActiveOracle\Query\Parser;

class Parser implements ParserInterface {

	private $sql = '';

	public function setSql($sql = '') {
		if ($sql != '') $this->sql = $sql;
	}

	public function getSql() {
		return $this->sql;
	}

	public function parse($clause = '') {
		
	}

}