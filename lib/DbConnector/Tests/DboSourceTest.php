<?php
namespace DbConnector\Tests;

use DbConnector\DboSource;

require_once '../../autoload.php';

class DboSourceTest extends \PHPUnit_Framework_TestCase {

	protected static $_dataSource = array(
		'username' => 'hr',
		'password' => 'root',
		'service' => '//localhost:1521',
		'persistent' => true
	);

	/**
	 * @before
	 * @runInSeparateProcess
	 */
	public function testInstanceOf() {
		$dbo = DboSource::connect();
		$this->assertInstanceOf('DbConnector\DboSource', $dbo);

		$dbo = new DboSource();
		$this->assertInstanceOf('DbConnector\DboSource', $dbo);
	}

	/**
	 * 
	 */
	public function testInstanceOfConnector() {
		$dbo_conn = DboSource::connect(array('connector' => 'oracle'));
		$connector = $dbo_conn->getConnector();
		
		$this->assertInstanceOf('DbConnector\Connector\OracleConnector', $connector);

		return $connector;
	}

	/**
	 * @depends testInstanceOfConnector
	 */
	public function testQueryFromSourceGettingConnector(\DbConnector\Connector\OracleConnector $connector = null) {
		$query = 'select * from dual';

		$connector->setDataSource(self::$_dataSource)->openConnection();
		$stid = $connector->query($query);

		$this->assertNotEmpty($stid);

		$result = $connector->fetchAll($stid);
		
		$this->assertNotEmpty($result);
		$this->assertInternalType('array', $result);
	}

}