<?php
namespace DataSource;

use DataSource\ConnectorInterface;

class DboSource implements ConnectorInterface {

	private $_dataSource = array();

	private $_connector = null;

	private static $_instance = null;

	public function __construct($dataSource = null) {
		if (!empty($dataSource['connector']) && $dataSource['connector'] != '') $this->setConnector($connector);
	}

	public static function connect($dataSource = null) {
		if (empty(self::$_instance)) {
			$obj = __CLASS__;
			self::$_instance = new $obj($dataSource);
		}

		return self::$_instance;
	}

	public function setConnector($connector) {
		$ns = __NAMESPACE__;
		$cn = ucfirst($connector).'Connector';
		$this->_connector = new $ns.'\\'.'Connector'.'\\'.$cn();

	}

	public function getConnector() {
		if (!empty($this->_connector)) return $this->_connector;
	}

}