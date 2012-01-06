<?php
/**
* Object TOOLBAR
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_toolbar extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"]      		= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["cellspacing"]	= array("value" => "0",  "inherit" => false, "html" => true);
		$this->property["cellpadding"]	= array("value" => "0",  "inherit" => false, "html" => true);
		$this->property["border"]		= array("value" => "0",  "inherit" => false, "html" => true);
		$this->property["java"]			= array("value" => "toolbar.js",  "inherit" => false, "html" => false);
		$this->property["cssfile"]		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsobj"]		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]		= array("value" => null, "inherit" => false, "html" => false);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$this->setCSS();
		return "\n$tab<table ".$this->getProperty("html", true, false)."><tr><td></td></tr></table>";
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$id = $this->property["id"]["value"];
		$this->setCSS();
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
			//Associazione eventi
			$this->addEvent($id, $dsobj."Move", "TOOLBAR.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "TOOLBAR.refreshObj(\"$id\");");
		}
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
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
}
?>
