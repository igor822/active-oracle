<?php
namespace DbConnector\Tests;

use DbConnector\Model as Model;

require_once '../../../vendor/autoload.php';

class ModelTest extends \PHPUnit_Framework_TestCase {

	protected static $_dataSource = array(
		'connector' => 'oracle',
		'username' => 'hr',
		'password' => 'root',
		'service' => '',
		'persistent' => true
	);

	protected $regionModel;

	protected function setUp() {
		$this->regionModel = new Model\Region(self::$_dataSource);
	}

	/*public function testConnectorInstanceFromModel() {
		$dbo_conn = new Model\Region(self::$_dataSource);
		$connector = $dbo_conn->getConnector()->openConnection();
		//var_dump($dbo_conn->getConnector()->openConnection()); 
		
		$this->assertInstanceOf('DbConnector\Connector\OracleConnector', $connector);
		$this->assertTrue($connector->isConnected());

		$result = $dbo_conn->fetch('select * from hr.regions');
		$this->assertInternalType('array', $result);

		$result = $dbo_conn->fetch('select * from hr.regions', 'object');
		$this->assertInstanceOf('ItemIterator\ItemIterator', $result);
	}

	public function testModelTable() {
		$region = new Model\Region(self::$_dataSource);

		$this->assertInstanceOf('DbConnector\Model\Region', $region);

		$result = $region->find('first', array('conditions' => array('region_id = 4')));
		
		$this->assertInternalType('array', $result);
	}*/

	public function testInsertQueryByModel() {
		$sql = $this->regionModel->insert(array('REGION_ID' => 12, 'REGION_NAME' => 'abc'), array('returning' => 'REGION_ID'));
		$sql = $this->regionModel->update(array('region_name' => 'bbbb'), array('conditions' => array('region_id = 10')));
		$sql = $this->regionModel->delete(array('conditions' => array('region_id = 12')));
		$this->assertTrue($sql);
	}

}