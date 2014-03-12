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

	public function testConnectorInstanceFromModel() {
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
	}

	public function testExpression() {
		$expr = new \DbConnector\DboExpression('aaaaa');

		$this->assertInstanceOf('DbConnector\DboExpression', $expr);

		$this->assertEquals('aaaaa', $expr->getValue());

		$sql = $this->regionModel->getQueryAdp()
								 ->select()
								 ->where('a = ?', array(new \DbConnector\DboExpression('count(*)')))
								 ->getSql();

		$this->assertEquals('SELECT * FROM regions p WHERE (a = count(*))', $sql);
	}

	public function testInsertExpression() {
		$sql = $this->regionModel->getQueryAdp('insert')
								 ->createQuery(array('aaa' => 'aaaaa', 'bbb' => new \DbConnector\DboExpression('sq_id_re.nextval')))
								 ->getSql();

		$this->assertEquals('INSERT INTO regions (aaa, bbb) VALUES (\'aaaaa\', sq_id_re.nextval)', $sql);
	}
}