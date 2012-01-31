<?php
/**
* Object LINK
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_link extends ClsObject {
	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id) 
	{
		$this->property = array();
		$this->property["id"]		= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["charset"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["href"]   	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["hreflang"] = array("value" => null, "inherit" => false, "html" => true);
		$this->property["media"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["rel"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["rev"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["target"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["type"]   	= array("value" => null, "inherit" => false, "html" => true);
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
		unset($this->property["id"]["value"]);
		$code = "\n".$tab."<link ".$this->getProperty("html", true, false)." />";
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