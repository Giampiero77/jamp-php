<?php
/**
* Object FIELDSET
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_tabs extends ClsObject {
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
		$this->property["id"] 	  	 	= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["tabs"]	  	 	= array("value" => $id,  "inherit" => false, "html" => false);
		$this->property["height"]   	= array("value" => "autosize", "inherit" => false, "html" => false);
		$this->property["width"]  	 	= array("value" => "autosize", "inherit" => false, "html" => false);
		$this->property["tabswidth"]	= array("value" => "", 	 "inherit" => false, "html" => false);
		$this->property["align"]  	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["selected"] 	= array("value" => null, "inherit" => false, "html" => false);	
 		$this->property["java"]  	  	= array("value" => "tabs.js", "inherit" => false, "html" => false);
		$this->property["cssfile"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->addEventListener("window", "resize", "Resize");
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		if ($this->property["width"]["value"] == "autosize") $this->property["style"]["value"] .= " width: auto;";
		else if (!empty($this->property["width"]["value"])) $this->property["style"]["value"] .= " width: ".$this->property["width"]["value"].";";

		if ($this->property["height"]["value"] == "autosize") $this->property["style"]["value"] .= " height: auto;";
		else if (!empty($this->property["height"]["value"])) $this->property["style"]["value"] .= " height: ".$this->property["height"]["value"].";";

		$id = $this->property["id"]["value"]."_title";
		$class = $this->property["class"]["value"]."_title";
		$class2 = $this->property["class"]["value"]."_box";

		//TITLE
		$code_title = "\n$tab\t<div id=\"$id\" class=\"$class\">";
		$br = "";
		$tabswidth = (!empty($this->property["tabswidth"]["value"])) ? "style=\"".$this->property["tabswidth"]["value"]."\"" : "";
		$align = (!empty($this->property["align"]["value"])) ? $this->property["align"]["value"] : "left";

		foreach ($this->child as $k => $tabs)
		{
		    $code_title .= "\n$tab\t\t<div class=\"$class2\" style=\"float: $align\" onclick=\"javascript:TABS.setFocus('".$this->property["id"]["value"]."', '$k');\">";   
			$code_title .= "\n$tab\t\t\t\t<table id=\"".$k."_tab\" class=\"unselected\" $tabswidth cellspacing=\"0\" cellpadding=\"0\">";
			$code_title .= "\n$tab\t\t\t\t\t<tr>";
			$code_title .= "\n$tab\t\t\t\t\t\t<td class=\"tab_left\"></td>";
			$code_title .= "\n$tab\t\t\t\t\t\t<td class=\"tab_text\">".$tabs->getPropertyName("label")."</td>";
			$code_title .= "\n$tab\t\t\t\t\t\t<td class=\"tab_right\"></td>";
			$code_title .= "\n$tab\t\t\t\t\t</tr>";
			$code_title .= "\n$tab\t\t\t\t</table>";   
    		$code_title .= "\n$tab\t\t</div>\n";
		}
		$code_title .= "\n$tab\t\t<div class=\"".$class."_bottom\"></div>";       
		$code_title .= "\n$tab\t</div>";

		
		//HTML BOX TAB
		$id = $this->property["id"]["value"]."_tabs";
		$class = $this->property["class"]["value"]."_tabs";
		$code_tabs = "\n$tab\t<div id=\"$id\" class=\"$class\">";
		if (!empty($this->property["value"]["value"])) $code_tabs .= "\n\t".$tab.$this->property["value"]["value"];
		foreach ($this->child as $obj)
		{
			$obj->setProperty("class", $this->property["class"]["value"]."_tab");
			$code_tabs .= $obj->codeHTML($tab."\t\t");
		}
		$code_tabs .= "\n$tab\t</div>";
		
		$code  = "\n$tab<div ".$this->getProperty("html", true, false).">";
		$code .= $code_title.$code_tabs;
		$code .= "\n$tab</div>";
 		return $code;
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		foreach ($this->child as $obj) $obj->codePDF($pdf);
	}

	/**
	* Generate the code text
	*/
	public function codeTXT()
	{    
		foreach ($this->child as $obj) $obj->codeTXT();
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$this->setCSS();
		$this->codejs = "";
		$id = $this->property["id"]["value"];
		if ($this->property["height"]["value"] == "autosize")
		{
			$this->property["height"]["value"] = "auto";
			$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
		}
		if ($this->property["width"]["value"] == "autosize")
		{
			$this->property["width"]["value"] = "auto";
			$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
		}
		$this->addEventBefore($id, "Resize", "TABS.sizeTabs(\"$id\");");
		if (!empty($this->property["selected"]["value"])) $this->setCodeJs("\t\tTABS.setFocus('".$id."', '".$this->property["selected"]["value"]."');");
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
	}
}
?>
