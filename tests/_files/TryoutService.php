<?php
function hello($sign)
{
	return "Hello$sign";
}
function abc($a, $b, $c = 'C')
{
	return "$a, $b, $c";
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
	 * A, B, C
	 * 
	 * @param string $a
	 * @param string $b
	 * @param string $c
	 * @return
	 */
	public function abc($a, $b, $c = 'C')
	{
		return "$a, $b, $c";
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