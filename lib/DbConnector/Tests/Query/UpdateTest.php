<?php
namespace DbConnector\Tests\Query;

use DbConnector\Query\Update;

require_once '../../../vendor/autoload.php';

class UpdateTest extends \PHPUnit_Framework_TestCase {

	public function testUpdateInstanceOf() {
		$update = new Update(array('table' => 'regions'));

		$this->assertInstanceOf('DbConnector\Query\Update', $update);

		return $update;
	}

	/**
	 * @depends testUpdateInstanceOf
	 */
	public function testUpdateQuery(\DbConnector\Query\Update $update) {
		$this->assertNotEmpty($update);

		$update->setValues(array('region_id' => 7, 'region_name' => 'AA'))
			   ->where('region_id = ?', array(6))
			   ->createQuery();

		$sql = 'UPDATE regions SET region_id = 7, region_name = "AA" WHERE (region_id = 6)';

		$this->assertEquals($sql, $update->getSql());
	}

}