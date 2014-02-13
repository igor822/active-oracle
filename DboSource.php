<?php
namespace DataSource;

use DataSource\ConnectionInterface;
use DataSource\Exception\ConnectorException;

class DboSource implements ConnectionInterface {

	private $_dataSource = array();

	private $_connector = null;

	private static $_instance = null;

	/**
	 * Constructor of object
	 *
	 * @param {array} $dataSource options of connection, is optional
	 * @return {object} instance of object
	 */
	public function __construct($dataSource = null) {
		if (!empty($dataSource['connector']) && $dataSource['connector'] != '') {
			$this->setConnector($dataSource['connector']);
		} 
	}

	/**
	 * Singleton to get an instance of object
	 * 
	 * @param {array} $dataSource options of connection, is optional
	 * @return {object} $instance
	 */
	public static function connect($dataSource = null) {
		if (empty(self::$_instance)) {
			$obj = __CLASS__;
			self::$_instance = new $obj($dataSource);
		}

		return self::$_instance;
	}

	/**
	 * Method to set a new connector data and get a new instance of object
	 *
	 * @param {string} Connector name by name of database, like, Oracle, MySql
	 * @access public
	 * @throws ConnectorException
	 * @return {object} $this
	 */
	public function setConnector($connector) {
		try {
			if (!empty($connector)) {
				$ns_class_name = __NAMESPACE__.'\\'.'Connector'.'\\'.(ucfirst($connector).'Connector');
				$this->_connector = new $ns_class_name();
			} else throw new ConnectorException('Connector not found', 1001);
		} catch(Exception $e) {
			var_dump($e->getMessage());
		}
		return $this;
	}

	/**
	 * Retrieve the instance of connector
	 * 
	 * @return {object} $connector instance of connector
	 */
	public function getConnector() {
		if (!empty($this->_connector)) return $this->_connector;
	}

}