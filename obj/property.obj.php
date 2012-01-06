<?php
/**
* Object PROPERTY
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_property extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["align"]  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["id"] 	  = array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["width"]  = array("value" => null, "inherit" => false, "html" => false);
		$this->property["height"] = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["java"]   = array("value" => "property.js", "inherit" => false, "html" => false);
 		$this->property["dsobj"]  = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"] = array("value" => null, "inherit" => false, "html" => false);
		$this->property["style"]["value"] = "overflow: auto;";
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
		$code = (!empty($this->property["label"]["value"])) ? $this->property["label"]["value"] : "";
		$old = $this->property["style"]["value"];
		$this->property["style"]["value"] = null;
		if (!empty($this->property["width"]["value"])) $this->property["style"]["value"] .= "width:".$this->property["width"]["value"].";";
		if (!empty($this->property["height"]["value"])) $this->property["style"]["value"] .= "height:".$this->property["height"]["value"].";";
		$this->property["style"]["value"] .= $old;
		return "\n$tab<div ".$this->getProperty("html", true, false).">\n$tab</div>";
	}
	
	/**
	* Builds the object
	*/
	public function BuildObj()	
	{	
		if (!empty($this->property["dsobj"]["value"]))
		{
			$id = $this->property["id"]["value"];
			$dsobj = $this->property["dsobj"]["value"];
			$this->addEvent($id, $dsobj."Move", "PROPERTY.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "PROPERTY.refreshObj(\"$id\");");
		}
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		switch($name)
		{
			case "height":
				if ($this->property["height"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["height"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
					$this->addEvent($id, "Resize", "PROPERTY.resize('$id');");
				}
			break;

			case "width":
				if ($this->property["width"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["width"]["value"] == "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
					$this->addEvent($id, "Resize", "PROPERTY.resize('$id');");
				}
			break;
		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "PROPERTY.refreshObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>