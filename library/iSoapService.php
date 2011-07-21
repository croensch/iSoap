<?php
/**
 * improved SOAP Service
 * 
 * @todo unwrap literal arrays
 * @todo tolerate missing parameters
 */
class iSoapService
{	
	/**
	 * @var mixed
	 */
	protected $_object;
	
	/**
	 * @var iSoapConfig
	 */
	protected $_config;
	
	/**
	 * @param mixed $object
	 * @param boolean $wrapped
	 */
	public function __construct($object, iSoapConfig $config)
	{
		$this->_object = $object;
		$this->_config = $config;
	}
	
	/**
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($name, array $arguments)
	{
		$method = new ReflectionMethod($this->_object, $name);
		
		if (SOAP_DOCUMENT === $this->_config->wsdl_binding_style) {
			$arguments = (array) $arguments[0];
		}
		
		try {
			$return = $method->invokeArgs($this->_object, $arguments);
		} catch (SoapFault $soapfault) {
			throw $soapfault;
		} catch (Exception $exception) {
			if (SOAP_1_2 === $this->_config->soap_version ) {
				$faultcode = 'env:Receiver';
			} else {
				$faultcode = 'SOAP-ENV:Server';
			}		
			if ($this->_config->send_errors) {
				$faultstring = (string) $exception;
			} else {
				$faultstring = 'Internal Error';
			}
			throw new SoapFault($faultcode, $faultstring);
		}
		
		if (SOAP_RPC === $this->_config->wsdl_binding_style || false === $this->_config->wrapped) {
			return $return;
		}
		
		$result = $name . 'Result';
		$response = new stdClass();
		$response->$result = $return;
		return $response;
	}
}