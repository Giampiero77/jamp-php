<?php
/**
* Object JMENU
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_jmenu extends ClsObject {
	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"]      	= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["dsobj"]   	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["java"]    	= array("value" => array("jmenu.js"),  "inherit" => false, "html" => false);
		$this->property["cssfile"] 	= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsobj"]  		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]		= array("value" => null, "inherit" => false, "html" => false);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		return "\n$tab<ul ".$this->getProperty("html", true, false)."><li></li></ul>";
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$this->setCSS();
		$id = $this->property["id"]["value"];
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
			$this->addEvent($id, $dsobj."Move", "JMENU.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "JMENU.refreshObj(\"$id\");");
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

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "\t\tsetTimeout(\"JMENU.refreshObj('".$this->property["id"]["value"]."')\",500);";
	}
}
?>
