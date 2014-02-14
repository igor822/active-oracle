<?php
namespace DbConnector;

use DbConnector\ConnectionInterface;
use DbConnector\Exception\ConnectorException;

use ItemIterator\ItemIterator;

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

	/**
	 * Method to run query and fetch result object
	 *
	 * @param {string} Query to search
	 * @return {array|object} $it Returns array or ItemIterator
	 * @access public
	 */
	public function fetch($query, $type = 'array') {
		$connector = $this->getConnector();
		$stid = $connector->query($query);

		$result = $connector->fetchAll($stid);

		$it = new ItemIterator($result);
		$rs = null;
		if ($type == 'array') $rs = $it->_toArray();
		else $rs = $it;

		$this->_afterFind($rs, $type);

		return $rs;
	}

	public function _afterFind($result, $type_rs = 'array') {
		if (method_exists($this, 'afterFind')) return $this->afterFind($result, $type_rs);
	}

}