<?php
namespace DbConnector;

use DbConnector\Exception\ConnectorException;

use ItemIterator\ItemIterator;

class DboSource {

	private $_dataSource = array();

	private $_connector = null;

	private static $_instance = null;

	const AUTO_COMMIT = true;

	const NO_AUTO_COMMIT = false;

	/**
	 * Constructor of object
	 *
	 * @param {array} $dataSource options of connection, is optional
	 * @return {object} instance of object
	 */
	public function __construct($dataSource = null) {
		if (isset($dataSource['username'])) $this->_dataSource = $dataSource;
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
				$this->_connector = new $ns_class_name($this->_dataSource);
			} else throw new ConnectorException('Connector not found', 1001);
		} catch(\Exception $e) {
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
	public function fetch($query, $type = 'array', $return = 'one', $autoCommit = true) {
		$connector = $this->getConnector();
		$stid = $connector->query($query, $autoCommit);

		if ($return = 'all') $rs = $connector->fetchAll($stid);
		else $rs = $connector->fetch($stid);

		$this->_call_event('afterFind', array(&$rs, $type));
		if ($type == 'object') $rs = new ItemIterator($rs);

		return $rs;
	}

	public function fetchAll($query, $type = 'array') {
		return $this->fetch($query, $type, 'all');
	}

	public function query($query) {
		try {
			$connector = $this->getConnector()->openConnection();
			$stid = $connector->query($query);
			$connector->clearStatement($stid);
		} catch (ConnectorException $e) {
			var_dump($e->getMessage());
		} catch (\Exception $e) {
			var_dump($e->getMessage());
		} 

		if ($stid) return true;
		return false;
	}

	public function prepare($query) {
		$connector = $this->getConnector()->openConnection();
		$stid = $connector->prepare($query);
		return $stid;
	}

	public function execute($stid) {
		$connector = $this->getConnector()->openConnection();
		$rs = $connector->execute($stid);
		return $rs;
	}

	/**
	 * Method to call child methods like callbacks
	 *
	 * @param {string} $name Name of event. Ex.: afterFind
	 * @param {array} $params Parameters of method
	 * @access protected
	 * @return {array} Resultset
	 */
	protected function _call_event($name, $params = array()) {
		if (method_exists($this, $name)) {
			$params[0] = call_user_func_array(array($this, $name), $params);
			return $params[0];
		}
	}

	/**
	 * Fetch last id added to new record.
	 *
	 * @param {string} $tableName Name of table to fetch last id
	 * @param {string} $fieldName Name of field considered primary key
	 * @return array
	 */
	public function lastInsertId($tableName, $fieldName) {
		$query = 'select max('.$tableName.') as last_id from '.$fieldName;
		return $this->fetch($query);
	}

}