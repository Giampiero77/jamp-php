<?php
/**
* Object MAP
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_map extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id) 
	{
		$this->property["id"] 	 	= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["libraries"] = array("value" => null, "inherit" => false, "html" => false);
		$this->property["lang"] 	= array("value" => "it", "inherit" => false, "html" => false);
		$this->property["style"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["address"] = array("value" => null, "inherit" => false, "html" => false);
		$this->property["lat"] 		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["lng"] 		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["zoom"] 	= array("value" => "13", "inherit" => false, "html" => false);
		$this->property["traffic"] = array("value" => null, "inherit" => false, "html" => false);
		$this->property["display"]	= array("value" => null, "inherit" => true, "html" => false);
		$this->property["marker"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["address"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["html"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["icon"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["draggable"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsmarker"]	= array("value" => null, "inherit" => false, "html" => false);

		$this->property["route"] 	= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsobj"]  	= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]	= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["java"]  	= array("value" => null, "inherit" => false, "html" => false);
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
	* Builds the object
	*/
	public function BuildObj()
	{
		$this->codejs = "";
		$id = $this->property["id"]["value"];
		$libraries = $this->property["libraries"]["value"];
		$lang = $this->property["lang"]["value"];


		$this->property["java"]["value"] = array("http://maps.google.com/maps/api/js?sensor=false&amp;language=$lang&amp;libraries=$libraries", "map.js");

	   if (!empty($this->property["display"]["value"])) $this->addEventBefore($id, $this->property["display"]["value"]."Display", "JMAP.displayObj('$id');");

		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
			$this->addEvent($id, $dsobj."Move", "JMAP.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "JMAP.refreshObj(\"$id\");");
		} 
		else $this->setCodeJs("JMAP.refreshObj(\"$id\");");
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		return "\n$tab<div ".$this->getProperty("html", true, false)."></div>";
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
 			case "zoom":
 			case "lat":
 			case "lng":
 			case "type":
 			case "traffic":
 			case "address":
 			case "route":
 			case "marker":
 			case "address":
 			case "html":
 			case "icon":
 			case "draggable":
 			case "dsmarker":
				$this->propertyJS[$name] = $this->property[$name]["value"];
 			break;	
 		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return (!empty($this->property["dsobj"]["value"])) ? "JMAP.refreshObj(\"".$this->property["id"]["value"]."\");" : "";
	}
}
?>
