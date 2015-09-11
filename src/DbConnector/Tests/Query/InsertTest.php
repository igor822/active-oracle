<?php
namespace ActiveOracle\Tests\Query;

use ActiveOracle\Query\Insert;

require_once '../../../vendor/autoload.php';

class InsertTest extends \PHPUnit_Framework_TestCase {

	public function testInsertInstanceOf() {
		$insert = new Insert(array('table' => 'regions'));

		$this->assertInstanceOf('DbConnector\Query\Insert', $insert);

		return $insert;
	}

	/**
	 * @depends testInsertInstanceOf
	 */
	public function testInsertQuery(\ActiveOracle\Query\Insert $insert) {
		$this->assertNotEmpty($insert);

		$insert->createQuery(array('region_id' => 7, 'region_name' => 'AA'));

		$sql = 'INSERT INTO regions (region_id, region_name) VALUES (7, "AA")';

		$this->assertEquals($sql, $insert->getSql());
	}

}