<?php 
namespace DbConnector\Query\Parser;

interface ParserInterface {

	public function setSql($sql = '');

	public function getSql();

	public function parse($clause = '');

}