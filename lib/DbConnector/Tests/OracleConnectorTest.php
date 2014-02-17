<?php
namespace DbConnector\Tests;

use DbConnector\Connector\OracleConnector;

require_once '../../autoload.php';

class OracleConnectorTest extends \PHPUnit_Framework_TestCase {

	protected static $_dataSource = array(
		'username' => 'hr',
		'password' => 'root',
		'service' => '//localhost:1521',
		'persistent' => true
	);

	public function testIfInstanceOf() {
		$ora_conn = OracleConnector::connect();
		$this->assertInstanceOf('DbConnector\Connector\OracleConnector', $ora_conn);

		return $ora_conn;
	}

	/**
	 * @depends testIfInstanceOf
	 */
	public function testCheckIfGetInstanceIsTheSame(OracleConnector $ora_conn = null) {
		$this->assertNotEmpty($ora_conn);

		$id = spl_object_hash($ora_conn);
		$this->assertEquals($id, spl_object_hash($ora_conn->getInstance()));

		return $ora_conn;
	}

	/**
	 * @depends testCheckIfGetInstanceIsTheSame
	 */
	public function testOpenCloseConnection(OracleConnector $ora_conn = null) {
		$ora_conn->setDataSource(self::$_dataSource);
		$conn = $ora_conn->openConnection();
		
		$this->assertTrue($ora_conn->isConnected(), 'Is connected');

		$ora_conn->closeConnection();
		$this->assertFalse($ora_conn->isConnected(), 'Is disconnected');


	}

	/**
	 * @depends testCheckIfGetInstanceIsTheSame
	 */
	public function testSomeQuery(OracleConnector $ora_conn = null) {
		$query = 'select * from hr.regions where rownum < 10';

		$ora_conn->setDataSource(self::$_dataSource)->openConnection();
		$stid = $ora_conn->query($query);

		$this->assertNotEmpty($stid);

		$result = $ora_conn->fetchAll($stid);
		
		$this->assertNotEmpty($result);
		$this->assertInternalType('array', $result);

	}

	/**
	 * @depends testCheckIfGetInstanceIsTheSame
	 */
	public function testSomeQueryInsert(OracleConnector $ora_conn = null) {
		$query = 'insert into hr.regions (REGION_NAME) values (\'Teste 123\')';
		$ora_conn->setDataSource(self::$_dataSource)->openConnection();

		$rs = $ora_conn->query($query);

		$this->assertNotEmpty($rs);
	}

}