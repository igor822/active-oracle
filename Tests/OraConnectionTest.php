<?php
namespace DataSource\OraConnection\Tests;

use DataSource\OraConnection;

require_once '../../autoload.php';

//require_once '../OraConnection.php';

class OraConnectionTest extends \PHPUnit_Framework_TestCase {

	public function testIfInstanceOf() {
		$ora_conn = OraConnection::connect();
		$this->assertInstanceOf('DataSource\OraConnection', $ora_conn);

		return $ora_conn;
	}

	/**
	 * @depends testIfInstanceOf
	 */
	public function testCheckIfGetInstanceIsTheSame(OraConnection $ora_conn = null) {
		$this->assertNotEmpty($ora_conn);

		$id = spl_object_hash($ora_conn);
		$this->assertEquals($id, spl_object_hash($ora_conn->getInstance()));

		return $ora_conn;
	}

	/**
	 * @depends testCheckIfGetInstanceIsTheSame
	 */
	public function testOpenCloseConnection(OraConnection $ora_conn = null) {
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
	public function testSomeQuery(OraConnection $ora_conn = null) {
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