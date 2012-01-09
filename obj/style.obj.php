<?php
/**
* Object STYLE
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_style extends ClsObject {
	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id) 
	{
		$this->property = array();
		$this->property["id"]		= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["type"]		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["media"]   	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["dir"]   	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["lang"]		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["title"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["value"]	= array("value" => null, "inherit" => false, "html" => false);
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
		$code = "\n".$tab."<style ".$this->getProperty("html", true, false).">".$this->property["value"]["value"]."</style>";
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
