<?php
/**
* Object PROGESSBAR
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_progressbar extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id) 
	{
		$this->property["id"] 	 	= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["percent"]	= array("value" => "0", "inherit" => false, "html" => false);
		$this->property["width"]   	= array("value" => "500px",  "inherit" => false, "html" => false);
		$this->property["label"]   	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["java"] 	= array("value" => "progessbar.js", "inherit" => false, "html" => false);
		$this->property["cssfile"] 	= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsobj"]  	= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]  	= array("value" => null, "inherit" => false, "html" => false);
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		if(!empty($this->property["dsobj"]["value"]))
		{
			global $xml;
			$dsObj = $xml->getObjById($this->property["dsobj"]["value"]);
			$row = $dsObj->ds->dsGetRow(0);
			$item = $this->property["dsitem"]["value"];
			$this->property["value"]["value"] = $row->$item;
		}
		$pdf->CellObj($this);
	}

	/**
	* Generate the code text
	*/
	public function codeTXT()
	{
		if(!empty($this->property["dsobj"]["value"]))
		{
			global $xml;
			$dsObj = $xml->getObjById($this->property["dsobj"]["value"]);
			$row = $dsObj->ds->dsGetRow(0);
			$item = $this->property["dsitem"]["value"];
			$this->property["value"]["value"] = $row->$item;
		}
		return $this->property["value"]["value"];
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
			$this->addEvent($id, $dsobj."Move", "PROGESSBAR.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "PROGESSBAR.refreshObj(\"$id\");");
		}
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$width 	 = intval($this->getPropertyName("width"));
		$percent = intval($this->getPropertyName("percent"));

		$code  = "<div style=\"width: ".($width+20)."\">";
		$code .= "<div class=\"Left_".$this->getPropertyName("class")."\"></div>";
		$code .= "<div class=\"Center_".$this->getPropertyName("class")."\" style=\"width: ".$width."px\">";
		$code .= "\n$tab<span ".$this->getProperty("html", true, false)." style=\"width: ".(($percent * $width)/100)."px\"></span>";
		$code .= "</div>";	
		$code .= "<div class=\"Right_".$this->getPropertyName("class")."\" style=\"left: ".($width+8)."px\"></div>";
		$code .= "<div id=\"".$this->getPropertyName("id")."_percent\"  class=\"Text_".$this->getPropertyName("class")."\" style=\"width: ".($width+20)."px\">".$percent."%</div>";
		$code .= "</div>";
		return $code;
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
			case "percent":
				$this->propertyJS[$name] = $this->property[$name]["value"];
			break;	
		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "PROGESSBAR.refreshObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>