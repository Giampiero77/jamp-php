<?php
/**
* Object LABEL
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_label extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id) 
	{
		$this->property["id"] 	 			= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["limit"] 	  		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["size"] 			= array("value" => null, "inherit" => false, "html" => false);
		$this->property["value"] 	 		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["align"] 	 		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["defaultvalue"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["label"]   	  		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["labelalign"]		= array("value" => "left",  "inherit" => false, "html" => false);
		$this->property["labelwidth"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelstyle"]		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["wrap"]				= array("value" => false,  "inherit" => false, "html" => false);
 		$this->property["format"]			= array("value" => null, "inherit" => false, "html" => false);
		$this->property["java"] 			= array("value" => array("label.js", "format.js"), "inherit" => false, "html" => false);
 		$this->property["dsobj"]  			= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]  			= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsobjlist"] 		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["dsitemlist"]   	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["dsitemkeylist"]	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["pdfimplode"]		= array("value" => " - ",  "inherit" => false, "html" => false);
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
			$items = explode(",",$this->property["dsitem"]["value"]);
			$values = "";
			foreach ($items as $item) $values[] = $row->$item;
			$this->property["value"]["value"] = implode($this->property["pdfimplode"]["value"], $values);
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
		$id = $this->property["id"]["value"];
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
			$this->addEvent($id, $dsobj."Move", "LABEL.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "LABEL.refreshObj(\"$id\");");
		}
		if (!empty($this->property["limit"]["value"])) $this->propertyJS["limit"] = $this->property["limit"]["value"];
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$code = "\n$tab<span ".$this->getProperty("html", true, false).">".str_replace('\n',"<br>",$this->property["value"]["value"])."</span>";
		$code = $this->getCodeLabelAlign($code, $tab);
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
			case "format":
			case "defaultvalue":
				$this->propertyJS[$name] = $this->property[$name]["value"];
			break;	

			case "dsitemkeylist":
				$this->propertyJS["dsItemKeyList"] = $this->property["dsitemkeylist"]["value"];
			break;

			case "dsitemlist":
				$this->propertyJS["dsItemList"] = $this->property["dsitemlist"]["value"];
			break;

			case "dsobjlist":    
				$this->propertyJS["dsObjList"] = $this->property["dsobjlist"]["value"];
			break;			
		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "LABEL.refreshObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>