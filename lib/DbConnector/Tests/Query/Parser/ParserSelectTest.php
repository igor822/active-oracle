<?php 
namespace DbConnector\Tests\Query\Parser;

use DbConnector\Query\Parser\ParseSelect;

require_once '../../../vendor/autoload.php';

class ParserSelectTest extends \PHPUnit_Framework_TestCase {

	public function testParserSelectClause() {
		$parser = new ParseSelect('SELECT a, b, c, de, efe 
									FROM aaa a, bbb b 
									inner join aa on aaa.aaa = aaa.aaa 
									inner join aaav on cddw =dcwddw
									inner join zzzz on aaaa = asas');
		$parser->parse('from');
		$parts = $parser->getParts();
		var_dump($parts);
		preg_match('/^(.*?)\s inner/', $parts['from'], $matches);
		var_dump($matches);
	}

}