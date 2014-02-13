<?php
namespace DataSource\Tests;

use DataSource\DboSource;

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
		$this->assertInstanceOf('DataSource\DboSource', $dbo);

		$dbo = new DboSource();
		$this->assertInstanceOf('DataSource\DboSource', $dbo);
	}

	/**
	 * 
	 */
	public function testInstanceOfConnector() {
		$dbo_conn = DboSource::connect(array('connector' => 'oracle'));
		$connector = $dbo_conn->getConnector();
		
		$this->assertInstanceOf('DataSource\Connector\OracleConnector', $connector);

		return $connector;
	}

	/**
	 * @depends testInstanceOfConnector
	 */
	public function testQueryFromSourceGettingConnector(\DataSource\Connector\OracleConnector $connector = null) {
		$query = 'select * from hr.regions where rownum < 10';

		$connector->setDataSource(self::$_dataSource)->openConnection();
		$stid = $connector->query($query);

		$this->assertNotEmpty($stid);

		$result = $connector->fetchAll($stid);
		
		$this->assertNotEmpty($result);
		$this->assertInternalType('array', $result);
	}

}