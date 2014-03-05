<?php
namespace DbConnector\Model;

use DbConnector\Model\Model;

class Region extends Model {

	protected $table = 'regions';

	public function __construct($dsorce) {
		parent::__construct($dsorce);
	}

	public function afterFind($result = null, $type = 'array') {
		foreach ($result as $row => $values) {
			$result[$row]['REGION_NAME'] = $values['REGION_NAME'].' sdcssds';
		}
		return $result;
	}

}