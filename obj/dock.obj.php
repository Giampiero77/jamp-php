<?php
/**
* Object DOCK
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_dock extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"] 		= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["class"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["valign"] 	= array("value" => "top", "inherit" => false, "html" => false);
		$this->property["align"] 	= array("value" => "center", "inherit" => false, "html" => false);
		$this->property["name"] 	= array("value" => null, "inherit" => false, "html" => true);
 		$this->property["java"]  	= array("value" => "dock.js", "inherit" => false, "html" => false);
 		$this->property["cssfile"]	= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsobj"]  	= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsrel"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsrev"] 	= array("value" => null, "inherit" => false, "html" => false);
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
		return "\n$tab<div ".$this->getProperty("html", true, false).">\n$tab</div>\n"; 
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
			//Associazione eventi
			$this->addEvent($id, $dsobj."Refresh", "DOCK.refreshObj(\"$id\");");
			$this->addEvent($id, $dsobj."Move", "DOCK.getDsValue(\"$id\");");
		}
	}
	
	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		$id = $this->property["id"]["value"];
		switch($name)
		{
			case "valign":
			case "align":
			case "dsrel":
			case "dsrev":
				$this->propertyJS[$name] = $this->property[$name]["value"];
			break;
		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "DOCK.refreshObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>