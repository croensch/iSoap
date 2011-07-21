<?php
/**
 * improved SOAP Configuration
 */
class iSoapConfig
{
	/**
	 * @var integer
	 */	
	public $soap_version = SOAP_1_2;
	
	/**
	 * @var integer
	 */	
	public $send_errors = 0;

	/**
	 * @var integer
	 */
	public $wsdl_binding_style = SOAP_DOCUMENT;

	/**
	 * @var integer
	 */
	public $wsdl_body_use = SOAP_LITERAL;

	/**
	 * @var boolean
	 */
	public $wrapped = true;
}