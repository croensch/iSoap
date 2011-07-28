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
	 * @var iSoapService
	 */
	protected $_service;
	
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
		$this->_config 	= new iSoapConfig;
		
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
		
		$this->_service = new iSoapService($this->_config);
				
		parent::__construct($wsdl, $options);	
		
		parent::setObject($this->_service);
	}
	
	/**
	 * @see SoapServer::addFunction()
	 * 
	 * @param mixed $functions
	 */
	public function addFunction($functions)
	{
		if (is_array($functions)) {
			foreach ($functions as $function){
				$this->addFunction($function);
			}
		}
		
		if (is_string($functions)) {
			if (function_exists($functions)) {
				$this->_service->functions[] = $functions;
				$this->_service->type = iSoapService::TYPE_FUNCTIONS;
			} else {
				trigger_error(__METHOD__."(): Tried to add a non existant function '$functions'", E_USER_WARNING);
			}
		}
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
		if (iSoapService::TYPE_FUNCTIONS === $this->_service->type) {
			return $this->_service->functions;
		}
		
		if (iSoapService::TYPE_CLASS === $this->_service->type) {
			return get_class_methods($this->_service->class_name);
		}
		
		if (iSoapService::TYPE_OBJECT === $this->_service->type) {
			return get_class_methods($this->_service->object);
		}
		
		return array();
	}
	
	/**
	 * @see SoapServer::setClass()
	 * 
	 * @param string $class_name
	 * @param string $args
	 */
	public function setClass($class_name, $args = null)
	{
		if (is_string($class_name) && strlen($class_name)){
			if (class_exists($class_name)) {
				$this->_service->class_name = $class_name;
				$this->_service->class_args = $args;
				$this->_service->type = iSoapService::TYPE_CLASS;
			} else {
				trigger_error(__METHOD__."(): Tried to set a non existant class ($class_name)", E_USER_WARNING);
			}
		}
	}
	
	/**
	 * @see SoapServer::setObject()
	 * 
	 * @param object $object
	 */
	public function setObject($object)
	{
		if (is_object($object)) {
			$this->_service->object = $object;
			$this->_service->type = iSoapService::TYPE_OBJECT;
		}
	}
}
