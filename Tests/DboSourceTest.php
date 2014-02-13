<?php
namespace DataSource\Tests;

use DataSource\DboSource;

require_once '../../autoload.php';

class DboSourceTest extends \PHPUnit_Framework_TestCase {

	public function testStaticInstanceOf() {
		$dbo = DboSource::connect(array());
		$this->assertInstanceOf('DataSource\DboSource', $dbo);
	}

}