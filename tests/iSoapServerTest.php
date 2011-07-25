<?php
require_once('iSoap.php');

require_once(__DIR__.'/_files/TryoutService.php');

class iSoapServerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var iSoapServer
	 */
	private $server;
	
	public function setUp()
	{
		parent::setUp();
		$this->server = new iSoapServer(__DIR__.'/_files/TryoutService.wsdl');
	}
	
	public function tearDown()
	{
		$this->server = null;
		parent::tearDown();
	}
	
	// Tests
	
	public function testConstruct()
	{
		$this->markTestIncomplete('todo: test rpc-encoded');
	}
	
	public function testAddFunction()
	{
		$this->markTestIncomplete();
	}
	
	public function testFault()
	{
		$this->markTestIncomplete();
	}
	
	public function testGetFunctions()
	{
		$this->markTestIncomplete();
	}
	
	public function testSetClass()
	{
		$this->server->setClass('TryoutService', array('Hello'));
		
		$this->serve('hello');
	}
	
	public function testSetObject()
	{
		$this->server->setObject(new TryoutService('Hello'));
		
		$this->serve('hello');
	}
	
	/**
	 * @param string $handle
	 * @return string
	 */
	protected function serve($handle, $handleResponse = null)
	{
		if (null === $handleResponse) {
			$handleResponse = $handle . 'Response';
		}
		
		ob_start();
		
		@$this->server->handle(file_get_contents(__DIR__."/_files/$handle.xml"));
		
		$this->assertXmlStringEqualsXmlFile(__DIR__."/_files/$handleResponse.xml",	ob_get_clean());
	}
}