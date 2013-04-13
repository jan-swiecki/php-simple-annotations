<?php

namespace DocBlockReader;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
	public function testParserOne()
	{
		$reader = new Reader($this, 'parserFixture');
		$parameters = $reader->getParameters();

		$this->assertNotEmpty($parameters);

		$this->assertArrayHasKey('number', $parameters);
		$this->assertArrayHasKey('string', $parameters);
		$this->assertArrayHasKey('array', $parameters);
		$this->assertArrayHasKey('object', $parameters);
		$this->assertArrayHasKey('nested', $parameters);
		$this->assertArrayHasKey('nestedArray', $parameters);
		$this->assertArrayHasKey('trueVar', $parameters);
		$this->assertArrayHasKey('null-var', $parameters);
		$this->assertArrayHasKey('booleanTrue', $parameters);
		$this->assertArrayHasKey('booleanFalse', $parameters);
		$this->assertArrayHasKey('booleanNull', $parameters);
		$this->assertArrayNotHasKey('non_existent_key', $parameters);

		$this->assertSame(1, $parameters['number']);
		$this->assertSame("123", $parameters['string']);
		$this->assertSame("abc", $parameters['string2']);
		$this->assertSame(array("a", "b"), $parameters['array']);
		$this->assertSame(array("x" => "y"), $parameters['object']);
		$this->assertSame(array("x" => array("y" => "z")), $parameters['nested']);
		$this->assertSame(array("x" => array("y" => array("z", "p"))), $parameters['nestedArray']);
		$this->assertSame(TRUE, $parameters['trueVar']);
		$this->assertSame(NULL, $parameters['null-var']);

		$this->assertSame(TRUE, $parameters['booleanTrue']);
		$this->assertSame(TRUE, $parameters['booleanTrue2']);
		$this->assertSame(FALSE, $parameters['booleanFalse']);
		$this->assertSame(NULL, $parameters['booleanNull']);
	}

	public function testParserTwo()
	{
		$reader = new Reader($this, 'parserFixture');

		$this->assertSame(1, $reader->getParameter('number'));
		$this->assertSame("123", $reader->getParameter('string'));
		$this->assertSame(array("x" => array("y" => array("z", "p"))),
			$reader->getParameter('nestedArray'));

		$this->assertSame(NULL, $reader->getParameter('nullVar'));
		$this->assertSame(NULL, $reader->getParameter('null-var'));
		$this->assertSame(NULL, $reader->getParameter('non-existent'));
	}

	/**
	 * @number 1
	 * @string "123"
	 * @string2 abc
	 * @array ["a", "b"]
	 * @object {"x": "y"}
	 * @nested {"x": {"y": "z"}}
	 * @nestedArray {"x": {"y": ["z", "p"]}}
	 *
	 * @trueVar
	 * @null-var null
	 *
	 * @booleanTrue true
	 * @booleanTrue2 tRuE
	 * @booleanFalse false
	 * @booleanNull null
	 * 
	 */
	private function parserFixture()
	{
	}

	public function testParserEmpty()
	{
		$reader = new Reader($this, 'parserEmptyFixture');
		$parameters = $reader->getParameters();
		$this->assertSame(array(), $parameters);
	}

	private function parserEmptyFixture()
	{
	}

	public function testParserMulti()
	{
		$reader = new Reader($this, 'parserMultiFixture');
		$parameters = $reader->getParameters();

		$this->assertNotEmpty($parameters);
		$this->assertArrayHasKey('param', $parameters);
		$this->assertArrayHasKey('var', $parameters);

		$this->assertSame("x",$parameters["var"]);
		$this->assertSame(1024,$parameters["var2"]);

		$this->assertSame(
			array("string x", "integer y", "array z"),
			$parameters["param"]);

	}

	/**
	 * @var x
	 * @var2 1024
	 * @param string x
	 * @param integer y
	 * @param array z
	 */
	private function parserMultiFixture()
	{
	}

	public function testParserThree()
	{
		$reader = new Reader($this, 'fixtureThree');
		// $allowedRequest = $reader->getParameter("allowedRequest");

		$postParam = $reader->getParameter("postParam");

		$this->assertNotEmpty($postParam);
	}

	/**
	 * @allowedRequest ["ajax", "post"]
	 * @postParam integer orderId
	 * @postParam array productIds
	 * @postParam string newValue
	 */
	private function fixtureThree()
	{

	}

	public function testParserFour()
	{
		$reader = new Reader($this, 'fixtureFour');

		$this->assertSame(TRUE, $reader->getParameter('get'));
		$this->assertSame(TRUE, $reader->getParameter('post'));
		$this->assertSame(TRUE, $reader->getParameter('ajax'));
		$this->assertSame(array("x","y","z"), $reader->getParameter('postParam'));
	}

	public function testParserFourBis()
	{
		$reader = new Reader($this, 'fixtureFour');

		$parameters = $reader->getParameters();

		$this->assertArrayHasKey('get', $parameters);
		$this->assertArrayHasKey('post', $parameters);
		$this->assertArrayHasKey('ajax', $parameters);
		$this->assertArrayHasKey('postParam', $parameters);

		$this->assertSame(TRUE, $parameters['get']);
		$this->assertSame(TRUE, $parameters['post']);
		$this->assertSame(TRUE, $parameters['ajax']);
		$this->assertSame(array("x","y","z"), $parameters['postParam']);

	}

	/**
	 * @get @post
	 * @ajax
	 * @postParam x
	 * @postParam y
	 * @postParam z
	 */
	private function fixtureFour()
	{
	}

	public function testFive()
	{
		$reader1 = new Reader($this, 'fixtureFive');
		$reader2 = new Reader($this, 'fixtureFive');

		$parameters1 = $reader1->getParameters();

		$trueVar1 = $parameters1['trueVar1'];

		$this->assertSame(TRUE,$trueVar1);
		$this->assertSame(TRUE,$reader2->getParameter("trueVar2"));

	}

	/**
	 * @trueVar1
	 * @trueVar2
	 */
	private function fixtureFive()
	{
	}

	public function testVariableDeclarations()
	{
		$reader = new Reader($this, 'fixtureVariableDeclarations');
		$declarations = $reader->getVariableDeclarations("param");
		$this->assertNotEmpty($declarations);

		$this->assertSame(array(
				array("type"=>"string", "name" => "var1"),
				array("type"=>"integer", "name" => "var2")
			), $declarations);
	}

	/**
	 * @param string var1
	 * @param integer var2
	 */
	private function fixtureVariableDeclarations()
	{
	}

	/**
	 * @dataProvider badVariableDataProvider
	 * @expectedException InvalidArgumentException
	 */
	public function testBadVariableDeclarations($methodName)
	{
		$reader = new Reader($this, $methodName);
		$declarations = $reader->getVariableDeclarations("param");
	}

	/**
	 * @param false
	 */
	private function fixtureBadVariableDeclarationsOne()
	{
	}

	/**
	 * @param true
	 */
	private function fixtureBadVariableDeclarationsTwo()
	{
	}

	public function badVariableDataProvider()
	{
		return array(
			array('fixtureBadVariableDeclarationsOne'),
			array('fixtureBadVariableDeclarationsTwo')
		);
	}
}