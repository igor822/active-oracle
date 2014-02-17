<?php
namespace DbConnector;

use DbConnector\DboSource;

class Model extends DboSource {

	public function __construct($dataSource) {
		parent::__construct($dataSource);
	}

	public static function connect($dataSource) {
		return parent::connect($dataSource);
	}

	public function afterFind($result = null, $type = 'array') {
		foreach ($result as $row => $values) {
			$result[$row]['REGION_NAME'] = $values['REGION_NAME'].' dsfegreghrhrhrhrh';
		}
		return $result;
	}

	public function query($sql) {}
}