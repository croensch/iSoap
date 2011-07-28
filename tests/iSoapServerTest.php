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
		$this->server = new iSoapServer(__DIR__.'/_files/TryoutService.wsdl', array('send_errors' => 1));
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
		$this->server->addFunction('hello');
		$this->serve('hello');
	}
	
	public function testAddFunctionBogus()
	{
		$this->setExpectedException(
			'PHPUnit_Framework_Error_Warning',
			//SoapServer::addFunction(): Tried to add a non existant function 'bogus'
			"iSoapServer::addFunction(): Tried to add a non existant function 'bogus'",
			E_USER_WARNING
		);
		$this->server->addFunction('bogus');
	}
	
	public function testFault()
	{
		$this->markTestIncomplete();
	}
	
	public function testGetFunctions()
	{
		$this->assertEmpty(
			$this->server->getFunctions()
		);
		$functions = array('hello');
		$this->server->addFunction($functions);
		$this->assertEquals($functions,	$this->server->getFunctions());
	}
	
	public function testGetFunctionsClass()
	{
		$class = 'TryoutService';
		$methods = get_class_methods($class);
		$this->server->setClass($class, array(''));
		$this->assertEquals($methods, $this->server->getFunctions());
	}
	
	public function testGetFunctionsObject()
	{
		$object = new TryoutService('');
		$methods = get_class_methods($object);
		$this->server->setObject($object);
		$this->assertEquals($methods, $this->server->getFunctions());
	}
	
	public function testSetClass()
	{
		$this->server->setClass('TryoutService', array('Hello'));
		$this->serve('hello');
	}
		
	public function testSetClassBogus()
	{
		$this->setExpectedException(
			'PHPUnit_Framework_Error_Warning',
			//SoapServer::setClass(): Tried to set a non existant class (Bogus)
			"iSoapServer::setClass(): Tried to set a non existant class (Bogus)",
			E_USER_WARNING
		);
		$this->server->setClass('Bogus');
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