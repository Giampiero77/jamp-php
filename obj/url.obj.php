<?php
/**
* Object URL
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_url extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id) 
	{
		$this->property["id"] 	 		= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["target"] 	  	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["action"] 	  	= array("value" => basename(__FILE__), "inherit" => false, "html" => false);
		$this->property["actionparam"]	= array("value" => "param", "inherit" => false, "html" => false);
		$this->property["size"] 	  	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["value"] 	 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["align"] 	 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["defaultvalue"] = array("value" => null, "inherit" => false, "html" => false);
		$this->property["label"]   		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["labelalign"]	= array("value" => "left",  "inherit" => false, "html" => false);
		$this->property["labelwidth"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelstyle"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["wrap"]   		= array("value" => false,  "inherit" => false, "html" => false);
		$this->property["format"] 		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["java"] 		= array("value" => array("url.js", "format.js"), "inherit" => false, "html" => false);
		$this->property["dsobj"]  		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsitem"]  	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["directory"]  = array("value" => null, "inherit" => false, "html" => false);
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		if (!empty($this->property["dsobj"]["value"]))
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
		if (!empty($this->property["dsobj"]["value"]))
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
			$this->addEvent($id, $dsobj."Move", "URL.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "URL.refreshObj(\"$id\");");
		}
		else 
		{
		  $this->setCodeJS("URL.refreshObj(\"$id\");");
		  $this->propertyJS["value"] = $this->property["value"]["value"];
		}
		$this->propertyJS["action"] = $this->property["action"]["value"];
		$this->propertyJS["actionparam"] = $this->property["actionparam"]["value"];
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$code = "\n$tab<span ".$this->getProperty("html", true, false)."></span>";
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
			case "target":
			case "directory":
				$this->propertyJS[$name] = $this->property[$name]["value"];
			break;	
		}
	}

	/**
	* Funzione di refresh dell'oggetto
	*/
	public function refreshOBJ()
	{
		return "URL.refreshObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>
