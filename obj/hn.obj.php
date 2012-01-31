<?php
/**
* Object HN (H1-H2...HN)
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_hn extends ClsObject {
	/**
	* @var $container Array contenente gli oggetti gestiti
	*/
	var $child = array();

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"] 	 = array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["type"] = array("value" => "h1", "inherit" => false, "html" => false);
		$this->property["class"] = array("value" => null, "inherit" => false, "html" => true);
		$this->property["value"] = array("value" => null, "inherit" => false, "html" => true);
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
	}

	/**
	* Generate the code text
	*/
	public function codeTXT()
	{
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$type = $this->getPropertyName("type");
		$code = "\n$tab<$type ".$this->getProperty("html", true, false).">";
		$code .= $this->getPropertyName("value");
		foreach ($this->child as $obj) $code .= "\n".$obj->codeHTML($tab);
		$code .= "\n$tab</$type>";
		return $code;
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
	}
}
?>