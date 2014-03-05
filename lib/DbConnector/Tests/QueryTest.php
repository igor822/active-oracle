<?php
namespace DbConnector\Tests;

use DbConnector\Query\Query;
use DbConnector\Query\QueryInterface;

require_once '../../../vendor/autoload.php';

class QueryTest extends \PHPUnit_Framework_TestCase {

	public function testQueryInstance() {
		$query = new Query(array('table' => 'regions'));
		$this->assertInstanceOf('DbConnector\Query\Query', $query);

		return $query;
	}

	/**
	 * @depends testQueryInstance
	 */
	public function testBuildSimpleSelect(\DbConnector\Query\Query $query) {
		$query->select('a, b')
			  ->join('test t', 't.a = a.a')
			  ->where('a = ? and aaa = ?', array("fff", 1))
			  ->where('a = ?', array('fffaaa'));
		
		$query->joinAll();

		$sql = $query->getSql();

		$q = 'SELECT a, b FROM regions REGIONS WHERE (a = \'fff\' and aaa = 1) AND (a = \'fffaaa\')';

		$this->assertEquals($q, $sql);
	}

}