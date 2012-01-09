<?php
/**
* Object META
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_meta extends ClsObject {

	/**
	* @var $container Array containing the child objects
	*/
	var $child = array();

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		unset($this->property);
		$this->property["id"] 			= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["name"]			= array("value" => null, "inherit" => false, "html" => true);
		$this->property["http-equiv"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["content"]		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["scheme"]		= array("value" => null, "inherit" => false, "html" => true);
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
		$code .= "\n<body ".$this->getProperty("html", true, false).">";
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		return "\n$tab<meta ".$this->getProperty("html", true, false)." />";
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