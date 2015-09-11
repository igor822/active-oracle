<?php

namespace ActiveOracle\Connector;

use ActiveOracle\Exception as DataSourceException;

class OracleConnector implements ConnectorInterface
{
    private $conn = null;

    private $query = null;

    private $dataSource = null;

    private $stid = null;

    private static $instance;

    /**
     * @param null $dataSource
     */
    public function __construct($dataSource = null)
    {
        $this->setDataSource($dataSource);
        self::$instance = $this;
    }

    /**
     * @param $dataSource
     * @return $this
     */
    public function setDataSource($dataSource)
    {
        if (is_array($dataSource)) {
            $this->dataSource = $dataSource;
        }
        return $this;
    }

    /**
     * Method to get current instance of object
     *
     * @access public
     * @return object $instance
     */
    public function getInstance()
    {
        return !empty(self::$instance) ? self::$instance : null;
    }

    /**
     * Connect with database, setting the datasource with data connection.
     * Returns instance of object
     *
     * @param {array} $dataSource Options to connection
     * @access static
     * @return object $instance
     */
    public static function connect($dataSource = null)
    {
        if (self::$instance == null) {
            $obj = __CLASS__;
            self::$instance = new $obj();
        }

        return self::$instance;
    }


    /**
     * Method for connection with database, setting the datasource like
     * The datasource has some options to configure
     * <code>
     * $dataSource = array(
     *      'username' => 'username',
     *      'password' => 'password',
     *      'service'  => '//host[:port][/service_name]'
     *      'charset'  => 'charset of connection' //(optional),
     *      'persistent' => boolean // Check if needs a persistent connection
     * );
     * </code>
     * For more information: http://www.php.net/manual/pt_BR/function.oci-connect.php
     *
     * @todo Add support to session_mode
     * @throws DataSourceException\ConnectionException
     * @access public
     * @return object $conn
     */
    public function openConnection()
    {
        if ($this->conn !== null) {
            return $this;
        }

        if (empty($this->dataSource['username']) && empty($this->dataSource['password'])) {
            throw new DataSourceException\ConnectionException([
                'message' => 'Missing connection data',
                'code' => 1001
            ]);
        }

        $oci_conn = 'oci_connect';
        if (!empty($this->dataSource['persistent']) && $this->dataSource['persistent'] === true) {
            $oci_conn = 'oci_pconnect';
        }

        $this->conn = $oci_conn(
                            $this->dataSource['username'],
                            $this->dataSource['password'],
                            $this->dataSource['service'],
                            (!empty($this->dataSource['charset']) ? $this->dataSource['charset'] : '')
                       );

        if (!$this->conn) {
            $err = oci_error($this->conn);
            throw new DataSourceException\ConnectionException($err);
        }

        return $this;
    }

    public function getConnection()
    {
        try {
            if (isset($this->conn)) {
                $this->openConnection();
            }
            return $this->conn;
        } catch (DataSourceException\ConnectionException $e) {
            var_dump($e->getMessage());
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    /**
     * Check if connection is alive
     *
     * @access public
     * @return boolean
     */
    public function isConnected()
    {
        return !empty($this->conn) ? true : false;
    }

    /**
     * Method to force close connection of database
     *
     * @param {mixed} $conn is optional
     * @return void
     */
    public function closeConnection(&$conn = null)
    {
        try {
            $conn = !empty($conn) ? $conn : $this->conn;
            oci_close($conn);
        } catch (\Exception $e) {
            error_log($e->getMessage(), 1);
        }
    }

    public function prepare($query)
    {
        if (!$this->isConnected()) {
            throw new DataSourceException\ConnectionException([
                'message' => 'Connector is not connected',
                'code' => 500
            ]);
        }
        $stid = oci_parse($this->conn, $query);
        return $stid;
    }

    public function execute($stid)
    {
        if (!$stid) {
            return null;
        }
        return oci_execute($stid);
    }

    public function query($query, $autoCommit = true)
    {
        if (!$this->isConnected()) {
            throw new DataSourceException\ConnectionException([
                'message' => 'Connector is not connected', 'code' => 500
            ]);
        }
        $stid = oci_parse($this->conn, $query);
        $constCommit = $autoCommit ? OCI_COMMIT_ON_SUCCESS : OCI_DEFAULT;
        oci_execute($stid, $constCommit);

        if ($autoCommit) {
            oci_commit($this->conn);
        }

        return $stid;
    }

    public function clearStatement($stid)
    {
        oci_free_statement($stid);
    }

    public function fetch($stid, $type = OCI_ASSOC)
    {
        if (empty($stid)) {
            throw new DataSourceException\StatementException();
        }
        $res = oci_fetch_array($stid, $type);
        oci_free_statement($stid);

        return $res;
    }

    public function fetchAll($stid, $type = OCI_FETCHSTATEMENT_BY_ROW)
    {
        if (empty($stid)) {
            throw new DataSourceException\StatementException();
        }

        $nrows = oci_fetch_all($stid, $res, 0, -1, $type);
        oci_free_statement($stid);

        return $res;
    }

    public function __destruct()
    {
        if (!empty($this->conn)) {
            oci_close($this->conn);
        }
    }

    public function bindParam($stid, $pname, &$variable, $type = SQLT_INT)
    {
        oci_bind_by_name($stid, ':'.$pname, $variable, -1, $type);
        return $this;
    }

    public function disconnect()
    {
        if (!empty($this->conn)) {
            oci_close($this->conn);
        }
    }
}
