<?php
/**
 * improved SOAP Service
 */
class iSoapService
{
	/**
	 * @var array
	 */
	protected $_options;

	/**
	 * @var array
	 */
	public $functions;

	/**
	 * @var string
	 */
	public $class_name;

	/**
	 * @var string
	 */
	public $class_args;

	/**
	 * @var mixed
	 */
	public $object;

	/**
	 * @var integer
	 */
	public $type;

	/**
	 * @var iSoapFault
	 */
	public $lastFault;

	/**
	 * @var integer
	 */
	const TYPE_CLASS = 1;

	/**
	 * @var integer
	 */
	const TYPE_FUNCTIONS = 2;

	/**
	 * @var integer
	 */
	const TYPE_OBJECT = 3;

	/**
	 * @param iSoapConfig $config
	 * @param integer $type
	 */
	public function __construct(array $options)
	{
		$this->_options = $options;
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($name, array $arguments)
	{
		if (SOAP_1_2 === $this->_options['soap_version'] ) {
			$faultcode = 'env:Receiver';
		} else {
			$faultcode = 'SOAP-ENV:Server';
		}

		try {
			switch ($this->type){
				case self::TYPE_FUNCTIONS:
					$reflectionFunction = new ReflectionFunction($name);
					break;
				case self::TYPE_CLASS:
					$reflectionClass = new ReflectionClass($this->class_name);
					$reflectionMethod = $reflectionClass->getMethod($name);
					break;
				case self::TYPE_OBJECT:
					$reflectionObject = new ReflectionObject($this->object);
					$reflectionMethod = $reflectionObject->getMethod($name);
					unset($reflectionObject);
					break;
			}
		} catch(ReflectionException $reflectionException) {
			unset($reflectionException);
			throw new SoapFault($faultcode, "Function '$name' doesn't exist");
		}

		if (SOAP_DOCUMENT === $this->_options['wsdl_binding_style']) {
			switch ($this->type){
				case self::TYPE_FUNCTIONS:
					$reflectionParameters = $reflectionFunction->getParameters();
					break;
				case self::TYPE_CLASS:
				case self::TYPE_OBJECT:
					$reflectionParameters = $reflectionMethod->getParameters();
					break;
			}
			$arguments[0] = (array) $arguments[0];
			foreach ($reflectionParameters as $reflectionParameter) {
				$key = $reflectionParameter->getName();
				if( isset($arguments[0][$key]) ){
					$arguments[$key] = $arguments[0][$key];
					unset($arguments[0][$key]);
				} else {
					$arguments[$key] = null;
				}
			}
			unset($reflectionParameters, $reflectionParameter);
			unset($arguments[0]);
		}

		$return = null;
		$soapfault = null;

		try {
			switch ($this->type){
				case self::TYPE_FUNCTIONS:
					$return = $reflectionFunction->invokeArgs($arguments);
					break;
				case self::TYPE_CLASS:
					if( null === $this->class_args ){
						$this->object = $reflectionClass->newInstance();
					} else {
						$this->object = $reflectionClass->newInstanceArgs($this->class_args);
					}
				case self::TYPE_OBJECT:
					$return = $reflectionMethod->invokeArgs($this->object, $arguments);
					break;
			}
		} catch (SoapFault $soapfault) {
		} catch (Exception $exception) {
			if ($this->_options['send_errors']) {
				$faultstring = (string) $exception;
			} else {
				$faultstring = 'Internal Error';
			}
			trigger_error(__METHOD__."(): caught $exception", E_USER_NOTICE);
			if (SOAP_1_2 === $this->_options['soap_version'] ) {
				$soapfault = new iSoapFault($faultcode, $faultstring);
			} else {
				$soapfault = new SoapFault($faultcode, $faultstring);
			}
		}

		if ($soapfault) {
			$this->lastFault = $soapfault;
			throw $soapfault;
		}

		if (SOAP_RPC === $this->_options['wsdl_binding_style'] || null === $this->_options['returnWrapper']) {
			return $return;
		}

		$result = $name . $this->_options['returnWrapper'];
		$response = new stdClass();
		$response->$result = $return;

		return $response;
	}
}
