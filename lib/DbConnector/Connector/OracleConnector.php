<?php
namespace DbConnector\Connector;

use DbConnector\Connector\ConnectorInterface;
use DbConnector\Exception as DataSourceException;

class OracleConnector implements ConnectorInterface {

	private $_conn = null;

	private $_query = null;

	private $_dataSource = null;

	private $_stid = null;

	private static $_instance;

	const PARAM_CHR = SQLT_CHR;

	const PARAM_INT = SQLT_INT;

	/**
	 * Constructor
	 *
	 * @param {array} $dataSource Options to connection
	 * @access public
	 * @return void
	 */
	public function __construct($dataSource = null) {
		$this->setDataSource($dataSource);
		self::$_instance = $this;
	}

	/**
	 * Set data source to connection to database
     * 
	 * @param {array} $dataSource Options to connection
	 * @return OraConnection
	 */
	public function setDataSource($dataSource) {
		if (is_array($dataSource)) {
			$this->_dataSource = $dataSource;
		}
		return $this;
	}

	/**
	 * Method to get current instance of object
	 *
	 * @access public
	 * @return object $instance
	 */
	public function getInstance() {
		return !empty(self::$_instance) ? self::$_instance : null; 
	}

	/**
	 * Connect with database, setting the datasource with data connection.
	 * Returns instance of object
	 * 
	 * @param {array} $dataSource Options to connection
	 * @access static
	 * @return object $instance
	 */
	public static function connect($dataSource = null) {
		if (self::$_instance == null) {
			$obj = __CLASS__;
			self::$_instance = new $obj();
		}
		//self::$_instance->setDataSource($dataSource);
		return self::$_instance;
	}


	/**
	 * Method for connection with database, setting the datasource like
	 * The datasource has some options to configure
	 * <code>
	 * $dataSource = array(
	 *		'username' => 'username',
	 *		'password' => 'password',
	 *		'service'  => '//host[:port][/service_name]'
	 * 		'charset'  => 'charset of connection' //(optional),
	 *		'persistent' => boolean // Check if needs a persistent connection
	 * );
	 * </code>
	 * For more information: http://www.php.net/manual/pt_BR/function.oci-connect.php
	 *
	 * @todo Add support to session_mode
	 * @access public
	 * @return object $conn
	 */
	public function openConnection() {
		try {

			if ($this->_conn !== null) return $this;

			if (empty($this->_dataSource['username']) && empty($this->_dataSource['password'])) { 
				throw new DataSourceException\ConnectionException(array('message' => 'Missing connection data', 'code' => 1001));
			}
							
			$oci_conn = 'oci_connect';
			if (!empty($this->_dataSource['persistent']) && $this->_dataSource['persistent'] === true) {
				$oci_conn = 'oci_pconnect';
			}

			$this->_conn = $oci_conn(
								$this->_dataSource['username'], 
								$this->_dataSource['password'], 
								$this->_dataSource['service'],
								(!empty($this->_dataSource['charset']) ? $this->_dataSource['charset'] : '')
						   );
			
			if (!$this->_conn) {
				$err = oci_error($this->_conn);
				throw new DataSourceException\ConnectionException($err);
			}

		} catch(DataSourceException\ConnectionException $e) {
			var_dump($e->getMessage());
		} catch (\Exception $e) {
			var_dump($e->getMessage());
		}
		return $this;
	}

	public function getConnection() {
		if (isset($this->_conn)) {
			$this->openConnection();
		}
		return $this->_conn;
	}


	/**
	 * Check if connection is alive
	 *
	 * @access public
	 * @return boolean
	 */
	public function isConnected() {
		return !empty($this->_conn) ? true : false;
	}

	/**
	 * Method to force close connection of database
	 *
	 * @param {mixed} $conn is optional
	 * @return void
	 */
	public function closeConnection(&$conn = null) {
		try {
			$conn = !empty($conn) ? $conn : $this->_conn;
			oci_close($conn);
		} catch (Exception $e) {
			error_log($e->getMessage, 1);
		}
	}

	public function prepare($query) {
		if (!$this->isConnected()) throw new DataSourceException\ConnectionException(array('message' => 'Connector is not connected', 'code' => 500));
		$stid = oci_parse($this->_conn, $query);
		return $stid;
	}

	public function execute($stid) {
		if (!$stid) return null;
		return oci_execute($stid);
	}

	public function query($query) {
		if (!$this->isConnected()) throw new DataSourceException\ConnectionException(array('message' => 'Connector is not connected', 'code' => 500));
		$stid = oci_parse($this->_conn, $query);
		
		oci_execute($stid, OCI_COMMIT_ON_SUCCESS);
		oci_commit($this->_conn);

		return $stid;
	}

	public function clearStatement($stid) {
		oci_free_statement($stid);
	}

	public function fetch($stid, $type = OCI_ASSOC) {
		if (empty($stid)) throw new DataSourceException\StatementException();
		$res = oci_fetch_array($stid, $type);
		oci_free_statement($stid);
		return $res;
	}

	public function fetchAll($stid, $type = OCI_FETCHSTATEMENT_BY_ROW) {
		if (empty($stid)) throw new DataSourceException\StatementException();

		$nrows = oci_fetch_all($stid, $res, 0, -1, $type);
		oci_free_statement($stid);
		return $res;
	}

	public function __destruct() {
		if (!empty($this->_conn)) oci_close($this->_conn);
	}

	public function bindParam($stid, $pname, &$variable, $type = SQLT_INT) {
		var_dump($pname);
		oci_bind_by_name($stid, ':'.$pname, $variable, -1, $type);
		return $this;
	}

}
