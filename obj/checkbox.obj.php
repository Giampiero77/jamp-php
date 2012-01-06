<?php
/**
* Object CHECKBOX
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_checkbox extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"]			= array("value" => $id,   "inherit" => false, "html" => true);
		$this->property["name"]			= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["value"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["accesskey"]	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onfocus"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onselect"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onblur"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onchange"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["tabindex"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["align"] 	  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["label"]		= array("value" => null,   "inherit" => false, "html" => false);
		$this->property["labelalign"]	= array("value" => "right",  "inherit" => false, "html" => false);
		$this->property["labelwidth"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelstyle"]	= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["check"]		= array("value" => "1", "inherit" => false, "html" => false);
 		$this->property["uncheck"]		= array("value" => "0", "inherit" => false, "html" => false);
 		$this->property["tristate"]		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["java"]			= array("value" => "checkbox.js", "inherit" => false, "html" => false);
 		$this->property["cssfile"]		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsobj"]		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]  		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["value"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["readonly"]		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["disabled"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["size"]			= array("value" => null, "inherit" => false, "html" => false);
		$this->property["checkimage"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["uncheckimage"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["tristateimage"]= array("value" => null, "inherit" => false, "html" => false);
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		global $system, $xml;
		$template = (!empty($this->property["template"]["value"])) ? $this->property["template"]["value"] : TEMPLATE;
		$this->property["src"]["value"] = $system->dir_real_jamp."/".$system->dir_template."objcss/$template/checkbox/";
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsObj = $xml->getObjById($this->property["dsobj"]["value"]);
			$row = $dsObj->ds->dsGetRow(0);
			$item = $this->property["dsitem"]["value"];
			$this->property["value"]["value"] = $row->$item;
		}
		if ($this->property["value"]["value"]==$this->property["check"]["value"]) $this->property["src"]["value"] .= "check.gif";
		else if ($this->property["value"]["value"]==$this->property["uncheck"]["value"]) $this->property["src"]["value"] .= "uncheck.gif";
		else $this->property["src"]["value"] .= "undefined.gif";
		$pdf->CellObj($this);
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
		$code = "\n$tab<span ".$this->getProperty("html", true, false).">&nbsp;</span>";
		$code = $this->getCodeLabelAlign($code, $tab);
		return $code;
	}
	
	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$this->codejs = "";
		$id = $this->property["id"]["value"];
		$this->propertyJS["classcheckbox"] = $this->setCSS();
		if (empty($this->property["dsobj"]["value"])) 
		{
			$this->property["onclick"]["value"] = "CHECKBOX.toogle(this); ".$this->property["onclick"]["value"];
			$this->setCodeJS("CHECKBOX.refreshObj(\"$id\");");
		}
		else
		{
			$dsobj = $this->property["dsobj"]["value"];
			$this->addEvent($id, $dsobj."Refresh", "CHECKBOX.refreshObj(\"$id\");");
			$this->addEvent($id, $dsobj."Move", "CHECKBOX.getDsValue(\"$id\");");
			if ($this->property['disabled']['value']!="true")
				$this->property["onclick"]["value"] = "CHECKBOX.setDsValue(this); ".$this->property["onclick"]["value"];
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
 			case "check":
 			case "uncheck":
 			case "tristate":
			case "value":
			case "name":
				$this->propertyJS[$name] = $this->property[$name]["value"];
 			break;
		  case "checkimage":
			 $this->propertyJS["checkImage"] = $this->property[$name]["value"];
		  break;
		  case "uncheckimage":
			 $this->propertyJS["uncheckImage"] = $this->property[$name]["value"];
		  break;
		  case "tristateimage":
			 $this->propertyJS["tristateImage"] = $this->property[$name]["value"];
		  break;	
			case "readonly":
				if ($this->property["readonly"]["value"] == "true") $this->propertyJS["Readonly"] = true;
 			break;
			case "disabled":
				if ($this->property["disabled"]["value"] == "true") $this->propertyJS["Disabled"] = true;
 			break;
		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return (empty($this->property["dsobj"]["value"])) ? "" : "CHECKBOX.refreshObj('".$this->property["id"]["value"]."');";
	}
}
?>
