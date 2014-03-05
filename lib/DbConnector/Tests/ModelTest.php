<?php
namespace DbConnector\Tests;

use DbConnector\Model as Model;

require_once '../../../vendor/autoload.php';

class ModelTest extends \PHPUnit_Framework_TestCase {

	protected static $_dataSource = array(
		'username' => 'hr',
		'password' => 'root',
		'service' => '//localhost:1521',
		'persistent' => true
	);

	public function testConnectorInstanceFromModel() {
		$dbo_conn = new Model\Model(array_merge(array('connector' => 'oracle'), self::$_dataSource));
		$connector = $dbo_conn->getConnector()->openConnection();
		
		$this->assertInstanceOf('DbConnector\Connector\OracleConnector', $connector);
		$this->assertTrue($connector->isConnected());

		$result = $dbo_conn->fetch('select * from hr.regions');
		$this->assertInternalType('array', $result);

		$result = $dbo_conn->fetch('select * from hr.regions', 'object');
		$this->assertInstanceOf('ItemIterator\ItemIterator', $result);
	}

	public function testModelTable() {
		$region = new Model\Region(array_merge(array('connector' => 'oracle'), self::$_dataSource));

		$this->assertInstanceOf('DbConnector\Model\Region', $region);

		$result = $region->find('first');
		//var_dump($region->getQueryAdp()->getSql());
		var_dump($result);
	}

}