<?php
namespace DbConnector\Tests;

use DbConnector\DboSource;
use DbConnector\Model;

//require_once '../../autoload.php';
require_once '../../../vendor/autoload.php';

class DboSourceTest extends \PHPUnit_Framework_TestCase {

	protected static $_dataSource = array(
		'username' => 'aplbradppf',
		'password' => '4pl1n1c0',
		'service' => 'CAMP2',
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

	public function testConnectorInstanceFromConnectSingleton() {
		$dbo_conn = DboSource::connect(array_merge(array('connector' => 'oracle'), self::$_dataSource));
		$connector = $dbo_conn->getConnector();

		$this->assertInstanceOf('DbConnector\Connector\OracleConnector', $connector);
		$this->assertTrue($connector->isConnected());

		$result = $dbo_conn->fetch('select * from hr.regions');
		$this->assertInternalType('array', $result);

		$result = $dbo_conn->fetch('select * from hr.regions', 'object');
		$this->assertInstanceOf('ItemIterator\ItemIterator', $result);
	}

	public function testConnectorInstanceFromModel() {
		$dbo_conn = new Model(array_merge(array('connector' => 'oracle'), self::$_dataSource));
		$connector = $dbo_conn->getConnector()->openConnection();

		$this->assertInstanceOf('DbConnector\Connector\OracleConnector', $connector);
		$this->assertTrue($connector->isConnected());

		$result = $dbo_conn->fetch('select * from hr.regions');
		$this->assertInternalType('array', $result);

		$result = $dbo_conn->fetch('select * from hr.regions', 'object');
		$this->assertInstanceOf('ItemIterator\ItemIterator', $result);
	}

}