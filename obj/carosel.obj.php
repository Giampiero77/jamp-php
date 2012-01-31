<?php
/**
* Object CAROSEL
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_carosel extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"]			= array("value" => $id,   "inherit" => false, "html" => true);
 		$this->property["width"]		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["height"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["name"]			= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onfocus"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onselect"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onblur"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onchange"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["tabindex"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["align"] 	  	= array("value" => null,  "inherit" => false, "html" => false);
 		$this->property["java"]			= array("value" => "carosel.js", "inherit" => false, "html" => false);
 		$this->property["cssfile"]		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsobj"]		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]  		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["value"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["speed"]		= array("value" => "40", "inherit" => false, "html" => false);
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
		$this->propertyJS["speed"] = $this->property["speed"]["value"];
  		if (!empty($this->property["width"]["value"])) $this->property["style"]["value"] = " width:".$this->property["width"]["value"].";";
  		if (!empty($this->property["height"]["value"])) $this->property["style"]["value"] .= " height:".$this->property["height"]["value"].";";
		return "\n$tab<div ".$this->getProperty("html", true, false).">&nbsp;</div>";
	}
	
	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$this->codejs = "";
		$this->setCSS();
		$id = $this->property["id"]["value"];
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
			$this->addEvent($id, $dsobj."Move", "CAROSEL.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "CAROSEL.refreshObj(\"$id\");");
		}	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		switch($name)
		{
			case "width":
				if ($this->property["width"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["width"]["value"] = "0px";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
				}
			break;

			case "height":
				if ($this->property["height"]["value"] == "autosize") $this->property["height"]["value"] = null;
			break;
		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "\t\tsetTimeout(\"CAROSEL.refreshObj('".$this->property["id"]["value"]."')\",500);";
	}
}
?>
