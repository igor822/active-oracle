<?php 
namespace DbConnector\Tests\Query\Parser;

use DbConnector\Query\Parser\ParseSelect;

require_once '../../../vendor/autoload.php';

class ParserSelectTest extends \PHPUnit_Framework_TestCase {

	public function testParserSelectClause() {
		$parser = new ParseSelect('SELECT a, b, c, de, efe 
									FROM aaa a, bbb b 
									inner join aaa aa ON aa = aaa 
									WHERE a = \'aa\'');
		$parser->parse('select');
		$select = $parser->getParts('select')['select'];
		
		$this->assertEquals('a, b, c, de, efe', $select);

		return $parser;
	}

	/**
	 * @depends testParserSelectClause
	 **/
	public function testPaserFromClause(\DbConnector\Query\Parser\ParseSelect $parser = null) {
		$this->assertNotEmpty($parser);

		$parser->parse('from');
		$this->assertEquals('aaa a, bbb b', $parser->getParts('from')['from']);

		return $parser;
	}

	/**
	 * @depends testParserSelectClause
	 **/
	public function testPaserWhereClause(\DbConnector\Query\Parser\ParseSelect $parser = null) {
		$parser->parse('where');
		$this->assertEquals('a = \'aa\'', $parser->getParts('where')['where']);
	}

}