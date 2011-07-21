<?php
/**
 * improved SoapServer
 */
class iSoapServer extends SoapServer
{
	/**
	 * @var iSoapConfig
	 */
	protected $_config;
	
	/**
	 * @see SoapServer::SoapServer()
	 * 
	 * default options
	 *  - soap_version			SOAP_1_1 or SOAP_1_2 (default)
	 *  - send_errors			integer (0 recommended)
	 * 
	 * extended options
	 *  - wsdl_binding_style	SOAP_RPC or SOAP_DOCUMENT (default)
	 *  - wsdl_body_use			SOAP_ENCODED or SOAP_LITERAL (default)
	 *  - wrapped				boolean (true recommended)
	 * 
	 * @param string $wsdl
	 * @param array $options
	 */
	public function __construct($wsdl = null, array $options = array())
	{		
		$this->_config = new iSoapConfig;
		
		if (isset($options['soap_version'])){
			$this->_config->soap_version = $options['soap_version'];
		} else {
			$options['soap_version'] = $this->_config->soap_version;
		}
		
		if (isset($options['send_errors'])){
			$this->_config->send_errors = $options['send_errors'];
		} else {
			$options['send_errors'] = $this->_config->send_errors;
		}

		if (isset($options['wsdl_binding_style'])) {
			$this->_config->wsdl_binding_style = $options['wsdl_binding_style'];
			unset($options['wsdl_binding_style']);
		}

		if (isset($options['wsdl_body_use'])) {
			$this->_config->wsdl_body_use = $options['wsdl_body_use'];
			unset($options['wsdl_body_use']);
		}
		
		if (isset($options['wrapped'])) {
			$this->_config->wrapped = $options['wrapped'];
			unset($options['wrapped']);
		}
		
		parent::__construct($wsdl, $options);
	}
	
	/**
	 * @see SoapServer::addFunction()
	 * 
	 * @param mixed $functions
	 */
	public function addFunction($functions)
	{
		/**
		 * @todo support functions
		 */
		parent::addFunction($functions);
	}
	
	/**
	 * @see SoapServer::fault()
	 * 
	 * @param string $code
	 * @param string $string
	 * @param string $actor
	 * @param string $details
	 * @param string $name
	 */
	public function fault($code, $string, $actor = null, $details = null, $name = null)
	{
		/**
		 * @todo improve SOAP_1_2
		 */
		parent::fault($code, $string, $actor, $details, $name);
	}
	
	/**
	 * @see SoapServer::getFunctions()
	 * 
	 * @return array
	 */
	public function getFunctions()
	{
		/**
		 * @todo support functions
		 */
		return parent::getFunctions();
	}
	
	/**
	 * @see SoapServer::setClass()
	 * 
	 * @param string $class_name
	 * @param array $args
	 */
	public function setClass($class_name, $args = null)
	{
		if (null === $args) {
			$object = new $class_name;
		} else {
			$reflectionClass = new ReflectionClass($class_name);
			$object = $reflectionClass->newInstanceArgs($args);
		}
		$this->setObject($object);
	}
	
	/**
	 * @see SoapServer::setObject()
	 * 
	 * @param mixed $object
	 */
	public function setObject($object)
	{
		$object = new iSoapService($object, $this->_config);
		parent::setObject($object);
	}
}
