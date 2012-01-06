<?php
/**
* Object PARAM
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_param extends ClsObject {
	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		unset($this->property);
		$this->property["id"] 		 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["name"]	 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["type"]		 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["value"]		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["valuetype"]	= array("value" => null, "inherit" => false, "html" => true);
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
		return "\n".$tab."<param ".$this->getProperty("html", true, false)."/>";
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