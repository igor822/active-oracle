<?php
namespace DataSource;

use DataSource\ConnectorInterface;

class DboSource implements ConnectorInterface {

	private $_dataSource = array();

	private $_connector = null;

	private static $_instance = null;

	public function __construct($dataSource = null) {
		if (!empty($dataSource['connector']) && $dataSource['connector'] != '') {
			$this->setConnector($dataSource['connector']);
		} 
	}

	public static function connect($dataSource = null) {
		if (empty(self::$_instance)) {
			$obj = __CLASS__;
			self::$_instance = new $obj($dataSource);
		}

		return self::$_instance;
	}

	public function setConnector($connector) {
		try {
			$ns_class_name = __NAMESPACE__.'\\'.'Connector'.'\\'.(ucfirst($connector).'Connector');
			$this->_connector = new $ns_class_name();
		} catch(Exception $e) {
			var_dump($e->getMessage());
		}
	}

	public function getConnector() {
		if (!empty($this->_connector)) return $this->_connector;
	}

}