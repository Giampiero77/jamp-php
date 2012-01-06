<?php
/**
* Object NOTE
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_note extends ClsObject {
	/**
	* @var $container Array containing the child objects
	*/
	var $child = array();

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)	
	{
		$this->property["align"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["id"]		= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["value"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["size"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["width"]	= array("value" => "161px", "inherit" => false, "html" => false);
		$this->property["height"]	= array("value" => "159px", "inherit" => false, "html" => false);
		$this->property["display"]	= array("value" => null, "inherit" => true,  "html" => false);
		$this->property["class"] 	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["cssfile"] = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["java"]  	= array("value" => "note.js", "inherit" => false, "html" => false);
		$this->property["display"] = array("value" => null, "inherit" => true, "html" => false);
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		if (!is_null($this->property["value"]["value"])) $pdf->CellObj($this);
		else if (is_array($this->child)) foreach ($this->child as $obj) $obj->codePDF($pdf);
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$this->codejs = "";
		$id = $this->property["id"]["value"];
		$this->setCSS();
		if (empty($this->property["display"]["value"])) $this->setCodeJs("\t\tNOTE.initObj(\"".$id."\");");
		else $this->addEventBefore($id, $this->property["display"]["value"]."Display", "NOTE.initObj(\"$id\");");
		$this->property["onmousedown"]["value"] = "NOTE.startMove(this, event); ".$this->property["onmousedown"]["value"];
	}

	/**
	* Generate the code text
	*/
	public function codeTXT()
	{
		foreach ($this->child as $obj) $obj->codeTXT();
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		global $system;
		if (!empty($this->property["width"]["value"])) $this->property["style"]["value"] .= "width:".$this->property["width"]["value"].";";
		if (!empty($this->property["height"]["value"])) $this->property["style"]["value"] .= "height:".$this->property["height"]["value"].";";
		$id = $this->property["id"]["value"];
		$code = (!empty($this->property["label"]["value"])) ? $this->property["label"]["value"] : "";
		$code .= "\n$tab<div ".$this->getProperty("html", true, false).">";
		$icon = $system->dir_web_jamp.$system->dir_template."objcss/".str_replace("note_", "", $this->property["class"]["value"])."/note/note.gif";
		$code .= "\n$tab\t<img class=\"".$this->property["class"]["value"]."\" src=\"".$icon."\" alt=\"\" width=\"100%\" height=\"100%\">";
		$code .= "\n$tab\t<div id=\"".$id."_container\" class=\"".$this->property["class"]["value"]."_container\">";
		if (!empty($this->property["value"]["value"])) $code .= "\n\t".$tab.$this->property["value"]["value"];
		foreach ($this->child as $obj) $code .= $obj->codeHTML($tab."\t\t");
		$code .= "\n$tab</div>";
		$code .= "\n$tab</div>";
		return $code;
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		$id = $this->property["id"]["value"];
		switch($name){
			case "height":
				if ($this->property["height"]["value"] == "autosize")
				{
					$this->property["height"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
				}
			break;
			case "width":
				if ($this->property["width"]["value"] == "autosize")
				{
					$this->property["width"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
				}
				if ($this->property["width"]["value"] == "autosize_center")
				{
					$this->property["width"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoWidthCenter('$id');");
				}
			break;
		}
	}
}
?>
