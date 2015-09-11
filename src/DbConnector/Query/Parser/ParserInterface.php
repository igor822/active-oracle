<?php 
namespace ActiveOracle\Query\Parser;

interface ParserInterface {

	public function setSql($sql = '');

	public function getSql();

	public function parse($clause = '');

}