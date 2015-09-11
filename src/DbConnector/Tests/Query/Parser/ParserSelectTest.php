<?php 
namespace ActiveOracle\Tests\Query\Parser;

use ActiveOracle\Query\Parser\ParseSelect;

require_once '../../../vendor/autoload.php';

class ParserSelectTest extends \PHPUnit_Framework_TestCase {

	public function testParserSelectClause() {
		$parser = new ParseSelect('SELECT a, b, c, de, efe
									FROM aaa a, bbb b 
									inner join aaa a ON a.aa = b.aa AND sdsd = sdsd
									WHERE a = \'aa\'
									group BY aaa
									order by vfvd DESC, vcwe');
		$parser->parse('select');
		$select = $parser->getParts('select');
		
		$this->assertEquals('a, b, c, de, efe', $select);

		return $parser;
	}

	/**
	 * @depends testParserSelectClause
	 **/
	public function testPaserFromClause(\ActiveOracle\Query\Parser\ParseSelect $parser = null) {
		$this->assertNotEmpty($parser);

		$parser->parse('from');
		//$this->assertEquals('aaa a, bbb b', $parser->getParts('from')['from']);

		return $parser;
	}

	/**
	 * @depends testParserSelectClause
	 **/
	public function testPaserWhereClause(\ActiveOracle\Query\Parser\ParseSelect $parser = null) {
		$parser->parse('where');
		$this->assertEquals('a = \'aa\'', $parser->getParts('where'));

		$parser->parse('join');
		//var_dump($parser->getParts('join'));

		$parser->parse('group');
		$parser->parse('order');

		var_dump($parser->getParts('join'));
	}

}