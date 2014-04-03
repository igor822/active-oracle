<?php 
namespace DbConnector\Query\Parser;

class ParseSelect implements ParserInterface {

	const RESERVED_WORDS = 'SELECT|FROM|INNER|OUTER|LEFT|RIGHT|WHERE|GROUP|ORDER';

	const PTRN_SELECT = '/(?!select)(\s(\n|.)*)|(?:\(.*\)))from/im';

	const PTRN_FROM = '/from([a-zA-Z0-9\s\w]+)\b(?:SELECT|FROM|INNER|OUTER|LEFT|RIGHT|WHERE|GROUP|ORDER)\b/im'

	// WHERE([a-zA-Z0-9\s\w\.\=\<\>\[\]\:]+)(?=\b(SELECT|FROM|INNER|OUTER|LEFT|RIGHT|WHERE|ORDER|GROUP)\b)(?(2)?(order)|)
	const PTRN_WHERE = '';

	const PTR_JOIN = '';

	private $sql = '';

	public function __construct($sql = '') {
		$this->setSql($sql);
	}

	public function setSql($sql = '') {
		if ($sql != '') $this->sql = $sql;
	}

	public function getSql() {
		return $this->sql;
	}

	public function parse($clause = 'select') {
		$pattern = ;
		
	}

}