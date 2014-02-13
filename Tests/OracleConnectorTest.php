<?php
namespace DataSource\Tests;

use DataSource\Connector\OracleConnector;

require_once '../../autoload.php';

class OracleConnectorTest extends \PHPUnit_Framework_TestCase {

	public function testIfInstanceOf() {
		$ora_conn = OracleConnector::connect();
		$this->assertInstanceOf('DataSource\Connector\OracleConnector', $ora_conn);

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
		$dataSource = array(
			'username' => 'hr',
			'password' => 'root',
			'service' => '//localhost:1521',
			'persistent' => true
		);

		$ora_conn->setDataSource($dataSource);
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

		$dataSource = array(
			'username' => 'hr',
			'password' => 'root',
			'service' => '//localhost:1521',
			'persistent' => true
		);

		$ora_conn->setDataSource($dataSource)->openConnection();
		$stid = $ora_conn->query($query);

		$this->assertNotEmpty($stid);

		$result = $ora_conn->fetch($stid);

		$this->assertNotEmpty($result);
		$this->assertInternalType('array', $result);

	}

}