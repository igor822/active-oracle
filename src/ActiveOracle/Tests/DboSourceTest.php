<?php

namespace ActiveOracle\Tests;

use ActiveOracle\DboSource;
use ActiveOracle\Model;
use ActiveOracle\Exception as DataSourceException;

class DboSourceTest extends \PHPUnit_Framework_TestCase
{
    protected static $dataSource = array(
        'username' => 'hr',
        'password' => 'root',
        'service' => '//localhost:1521',
        'persistent' => true
    );

    /**
     * @before
     * @runInSeparateProcess
     */
    public function testInstanceOf()
    {
        $dbo = DboSource::connect();
        $this->assertInstanceOf('ActiveOracle\DboSource', $dbo);

        $dbo = new DboSource();
        $this->assertInstanceOf('ActiveOracle\DboSource', $dbo);
    }

    public function testInstanceOfConnector()
    {
        $dbo_conn = DboSource::connect(array_merge(array('connector' => 'oracle'), self::$dataSource));
        $connector = $dbo_conn->getConnector();

        $this->assertInstanceOf('ActiveOracle\Connector\OracleConnector', $connector);

        return $connector;
    }

    /**
     * @depends testInstanceOfConnector
     */
    public function testQueryFromSourceGettingConnector(\ActiveOracle\Connector\OracleConnector $connector = null)
    {
        $query = 'select * from dual';

        $connector->setDataSource(array_merge(array('connector' => 'oracle'), self::$dataSource))->openConnection();
        $stid = $connector->query($query);

        $this->assertNotEmpty($stid);

        $result = $connector->fetchAll($stid);

        $this->assertNotEmpty($result);
        $this->assertInternalType('array', $result);
    }

    public function testConnectorInstanceFromConnectSingleton()
    {
        $dbo_conn = DboSource::connect(array_merge(array('connector' => 'oracle'), self::$dataSource));
        $connector = $dbo_conn->getConnector()->openConnection();

        $this->assertInstanceOf('ActiveOracle\Connector\OracleConnector', $connector);
        $this->assertTrue($connector->isConnected());

        $result = $dbo_conn->fetch('select * from hr.regions');
        $this->assertInternalType('array', $result);

        $result = $dbo_conn->fetch('select * from hr.regions', 'object');
        $this->assertInstanceOf('ItemIterator\ItemIterator', $result);
    }

    public function testConnectorInstanceFromModel()
    {
        $dbo_conn = new Model(array_merge(array('connector' => 'oracle'), self::$dataSource));
        $connector = $dbo_conn->getConnector()->openConnection();

        $this->assertInstanceOf('ActiveOracle\Connector\OracleConnector', $connector);
        $this->assertTrue($connector->isConnected());

        $result = $dbo_conn->fetch('select * from hr.regions');
        $this->assertInternalType('array', $result);

        $result = $dbo_conn->fetch('select * from hr.regions', 'object');
        $this->assertInstanceOf('ItemIterator\ItemIterator', $result);
    }
}
