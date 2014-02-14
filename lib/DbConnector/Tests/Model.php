<?php
namespace DbConnector\Tests;

use DbConnector\DboSource;

class Model extends DboSource {

	public function __construct($dataSource) {
		parent::__construct($dataSource);
	}

	public static function connect($dataSource) {
		return parent::connect($dataSource);
	}

	public function afterFind($result, $type) {
		if ($type == 'object') $result = $type->_toArray();
		foreach ($result as $row => $values) {
			$result[$row]['REGION_NAME'] = $values['REGION_NAME'].' adadwdwdw';
		}
		return $result;
	}

}