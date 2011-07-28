<?php
function hello($sign)
{
	return "Hello$sign";
}
class TryoutService
{
	/**
	 * @param string $text
	 */
	public function __construct($text)
	{
		$this->text = $text;
	}
	
	/**
	 * Hello!?
	 * 
	 * @param string $sign
	 * @return string
	 */
	public function hello($sign)
	{
		return "$this->text$sign";
	}
	
	/**
	 * Crash
	 * 
	 * @throws SoapFault
	 */
	public function throwFault()
	{
		throw new SoapFault('faultcode', 'faultstring', 'faultactor', 'detail', 'faultname', 'headerfault');
	}
	
	/**
	 * Crash
	 * 
	 * @throws Exception
	 */
	public function throwException($message, $code)
	{
		throw new Exception($message, $code);
	}
	
	/**
	 * Crash
	 * 
	 * @param integer $level
	 * @return integer
	 */
	public function triggerError($level)
	{
		trigger_error('message', $level);
		return $level;
	}
}