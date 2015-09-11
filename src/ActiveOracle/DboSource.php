<?php

namespace ActiveOracle;

use ActiveOracle\Connector\ConnectorInterface;
use ActiveOracle\Exception\ConnectorException;
use ItemIterator\ItemIterator;

class DboSource
{
    private $dataSource = array();

    private $connector = null;

    private static $instance = null;

    const AUTO_COMMIT = true;

    const NO_AUTO_COMMIT = false;

    /**
     * Constructor of object
     *
     * @param {array} $dataSource options of connection, is optional
     */
    public function __construct($dataSource = null)
    {
        if (isset($dataSource['username'])) {
            $this->dataSource = $dataSource;
        }
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
    public static function connect($dataSource = null)
    {
        if (empty(self::$instance)) {
            $obj = __CLASS__;
            self::$instance = new $obj($dataSource);
        }

        return self::$instance;
    }

    /**
     * Method to set a new connector data and get a new instance of object
     *
     * @param {string} Connector name by name of database, like, Oracle
     * @throws ConnectorException
     * @return $this
     */
    public function setConnector($connector)
    {
        try {
            if (empty($connector)) {
                throw new ConnectorException('Connector not found', 1001);
            }

            $ns_class_name = __NAMESPACE__.'\\'.'Connector'.'\\'.(ucfirst($connector).'Connector');
            $this->connector = new $ns_class_name($this->dataSource);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return $this;
    }

    /**
     * Retrieve the instance of connector
     *
     * @return ConnectorInterface
     */
    public function getConnector()
    {
        if (!empty($this->connector)) {
            return $this->connector;
        }
    }

    /**
     * @param $query
     * @param string $type
     * @param string $return
     * @param bool $autoCommit
     * @return ItemIterator
     */
    public function fetch($query, $type = 'array', $return = 'one', $autoCommit = self::AUTO_COMMIT)
    {
        $connector = $this->getConnector();
        $stid = $connector->query($query, $autoCommit);

        if ($return = 'all') {
            $rs = $connector->fetchAll($stid);
        } else {
            $rs = $connector->fetch($stid);
        }

        $this->callEvent('afterFind', array(&$rs, $type));
        if ($type == 'object') {
            $rs = new ItemIterator($rs);
        }

        return $rs;
    }

    public function fetchAll($query, $type = 'array')
    {
        return $this->fetch($query, $type, 'all');
    }

    public function query($query)
    {
        $stid = null;
        try {
            $connector = $this->getConnector()
                              ->openConnection();

            $stid = $connector->query($query);
            $connector->clearStatement($stid);
        } catch (ConnectorException $e) {
            var_dump($e->getMessage());
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

        if ($stid) {
            return true;
        }
        return false;
    }

    public function prepare($query)
    {
        $connector = $this->getConnector()->openConnection();
        $stid = $connector->prepare($query);
        return $stid;
    }

    public function execute($stid)
    {
        $connector = $this->getConnector()->openConnection();
        $rs = $connector->execute($stid);
        return $rs;
    }

    /**
     * @param $name
     * @param array $params
     * @return mixed|null
     */
    protected function callEvent($name, $params = array())
    {
        if (!method_exists($this, $name)) {
            return null;
        }

        $params[0] = call_user_func_array(array($this, $name), $params);
        return $params[0];
    }

    /**
     * Fetch last id added to new record.
     *
     * @param {string} $tableName Name of table to fetch last id
     * @param {string} $fieldName Name of field considered primary key
     * @return array
     */
    public function lastInsertId($tableName, $fieldName)
    {
        $query = 'select max('.$tableName.') as last_id from '.$fieldName;
        return $this->fetch($query);
    }
}
