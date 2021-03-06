<?php

namespace ActiveOracle\Tests\Connector;

use ActiveOracle\Connector\OracleConnector;

class OracleConnectorTest extends \PHPUnit_Framework_TestCase
{
    protected static $dataSource = array(
        'username' => 'system',
        'password' => 'oracle',
        'service' => 'localhost:49161',
        'persistent' => true
    );

    public function testIfInstanceOf()
    {
        $ora_conn = OracleConnector::connect();
        $this->assertInstanceOf('ActiveOracle\Connector\OracleConnector', $ora_conn);

        return $ora_conn;
    }

    /**
     * @depends testIfInstanceOf
     */
    public function testCheckIfGetInstanceIsTheSame(OracleConnector $ora_conn = null)
    {
        $this->assertNotEmpty($ora_conn);

        $id = spl_object_hash($ora_conn);
        $this->assertEquals($id, spl_object_hash($ora_conn->getInstance()));

        return $ora_conn;
    }

    /**
     * @depends testCheckIfGetInstanceIsTheSame
     */
    public function testOpenCloseConnection(OracleConnector $ora_conn = null)
    {
        $ora_conn->setDataSource(self::$dataSource);
        $conn = $ora_conn->openConnection();

        $this->assertTrue($ora_conn->isConnected(), 'Is connected');

        $ora_conn->closeConnection();
        $this->assertFalse($ora_conn->isConnected(), 'Is disconnected');


    }

    /**
     * @depends testCheckIfGetInstanceIsTheSame
     */
    public function testSomeQuery(OracleConnector $ora_conn = null)
    {
        $query = 'select * from dual where rownum < 10';

        $ora_conn->setDataSource(self::$dataSource)->openConnection();
        $stid = $ora_conn->query($query);

        $this->assertNotEmpty($stid);

        $result = $ora_conn->fetchAll($stid);

        $this->assertNotEmpty($result);
        $this->assertInternalType('array', $result);
    }


    public function testInstanceWithoutSourceInformation()
    {
        $ora_conn = new OracleConnector();

        $this->assertInstanceOf('ActiveOracle\Connector\OracleConnector', $ora_conn);
        $this->assertFalse($ora_conn->isConnected());

        $ora_conn->setDataSource(self::$dataSource)->openConnection();

        $this->assertTrue($ora_conn->isConnected());

        $ora_conn->disconnect();

        $this->assertFalse($ora_conn->isConnected());

        $conf = array(
            'username' => 'system',
            'password' => 'oracle',
            'service' => 'localhost:49161',
            'persistent' => true
        );

        $ora_conn->setDataSource($conf)->openConnection();

        $this->assertTrue($ora_conn->isConnected());
    }
}
