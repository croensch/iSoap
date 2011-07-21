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
	 * options
	 *  - wsdl_binding_style
	 *  - wsdl_body_use
	 * 
	 * @param string $wsdl
	 * @param array $options
	 */
	public function __construct($wsdl = null, array $options = array())
	{
		if (null === $wsdl) {
			$schema = "http";
	        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
	            $schema = 'https';
	        }
			if(isset($_SERVER['HTTP_HOST'])) {
	            $host = $_SERVER['HTTP_HOST'];
	        } else {
	            $host = $_SERVER['SERVER_NAME'];
	        }
			if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
	            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
	        } elseif (isset($_SERVER['REQUEST_URI'])) {
	            $requestUri = $_SERVER['REQUEST_URI'];
	        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
	            $requestUri = $_SERVER['ORIG_PATH_INFO'];
	        } else {
	            $requestUri = $_SERVER['SCRIPT_NAME'];
	        }
	        if( ($pos = strpos($requestUri, "?")) !== false) {
	            $requestUri = substr($requestUri, 0, $pos);
	        }
	        $wsdl = $schema . '://' . $host . $requestUri;
		}
		
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
		 * @todo Functions
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
		 * @todo SOAP_1_2
		 */
		parent::fault($code, $string, $actor, $details, $name);
	}
	
	/**
	 * @see SoapServer::getFunctions()
	 */
	public function getFunctions()
	{
		/**
		 * @todo Functions
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