<?php
namespace DataSource\Tests;

use DataSource\DboSource;

require_once '../../autoload.php';

class DboSourceTest extends \PHPUnit_Framework_TestCase {

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
	}

}