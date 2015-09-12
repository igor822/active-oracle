<?php

namespace ActiveOracle\Tests\Query;

use ActiveOracle\Query\Query;
use ActiveOracle\Query\QueryInterface;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    public function testQueryInstance()
    {
        $query = new Query(array('table' => 'regions'));
        $this->assertInstanceOf('ActiveOracle\Query\Query', $query);

        return $query;
    }

    /**
     * @depends testQueryInstance
     */
    public function testBuildSimpleSelect(\ActiveOracle\Query\Query $query)
    {
        $query->select('a, b')
              ->join('test t', 't.a = a.a')
              ->where('a = ? and aaa = ?', array("fff", 1))
              ->where('a = ?', array('fffaaa'));

        $query->joinAll();

        $sql = $query->getSql();

        $q = 'SELECT a, b FROM regions REGIONS INNER JOIN test t ON t.a = a.a WHERE (a = \'fff\' and aaa = 1) AND (a = \'fffaaa\')';

        $this->assertEquals($q, $sql);
    }
}
