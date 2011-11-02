<?php
/**
 * @todo implement subcode
 */
class iSoapFault extends SoapFault
{
	/**
	 * @var mixed
	 */
	public $subcodevalue;

	/**
	 * @var string
	 */
	public $node;

	/**
	 * @var string
	 */
	public $role;

	/**
	 * @var string
	 */
	public $language;

	/**
	 * @see SoapFault::SoapFault()
	 * 
	 * @param string $codevalue
	 * @param string $reasontext
	 * @param string $node
	 * @param string $role
	 * @param mixed $detail
	 * @param mixed $subcodevalue
	 * @param mixed $language
	 */
	public function __construct($codevalue, $reasontext, $node = null, $role = null, $detail = null, $subcodevalue = null, $language = 'x-unknown')
	{
		parent::__construct($codevalue, $reasontext, null, $detail);

		$this->subcodevalue = $subcodevalue;

		$this->node = $node;
		$this->role = $role;

		$this->language = $language;
	}

	/**
	 * @return string
	 */
	public function getCodevalue()
	{
		return $this->faultcode;
	}

	/**
	 * @return string
	 */
	public function getReasontext()
	{
		return $this->faultstring;
	}

	/**
	 * @param string $xml
	 */
	public function fixSoap12(&$xml)
	{
		$document = new DOMDocument();
		$document->loadXML($xml);

		$nsURI	= 'http://www.w3.org/2003/05/soap-envelope';

		if ($this->subcodevalue) {
			$code = $document->getElementsByTagNameNs($nsURI, "Code")->item(0);
			$subcode = $document->createElementNS($nsURI, 'Subcode');
			$value = $document->createElementNS($nsURI, 'Value');
			$value->appendChild($document->createTextNode($this->subcodevalue));
			$subcode->appendChild($value);
			$code->appendChild($subcode);
		}

		$text = $document->getElementsByTagNameNS($nsURI, "Text")->item(0);
		$text->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:lang', $this->language);

		if ($this->node || $this->role) {
			$fault = $document->getElementsByTagNameNS($nsURI, "Fault")->item(0);
			$detail = $document->getElementsByTagNameNS($nsURI, "Detail")->item(0);
			if ($detail) {
				$fault->removeChild($detail);
			}
			if ($this->node) {
				$node = $document->createElementNS($nsURI, 'Node');
				$node->appendChild($document->createTextNode($this->node));
				$fault->appendChild($node);
			}
			if ($this->role) {
				$role = $document->createElementNS($nsURI, 'Role');
				$role->appendChild($document->createTextNode($this->role));
				$fault->appendChild($role);
			}
			if ($detail) {
				$fault->appendChild($detail);
			}
		}

		$xml = $document->saveXML();
	}
}