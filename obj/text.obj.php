<?php
/**
* Object TEXT
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_text extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["keypress"]  		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["blur"]  	  		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["send"]  	  		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["minlength"] 		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["name"] 	  	 	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["value"] 	  		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["defaultvalue"]	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["size"] 	  		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["maxlength"] 		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["alt"] 		  		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["src"] 		 		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["align"] 	  		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["accesskey"] 		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onfocus"]   		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onselect"]  		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onblur"] 	  		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onchange"]  		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["tabindex"] 		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["id"] 				= array("value" => $id,   "inherit" => false, "html" => true);
		$this->property["password"] 		= array("value" => false, "inherit" => false, "html" => false);
		$this->property["fileupload"] 	= array("value" => false, "inherit" => false, "html" => false);
		$this->property["rewrite"]			= array("value" => true,  "inherit" => false, "html" => false); //true,false,rename
		$this->property["target"]  		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["directory"] 	 	= array("value" => dirname($_SERVER['PHP_SELF'])."/", "inherit" => false, "html" => false);
		$this->property["dsobj"] 	  		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["label"] 	 		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelalign"]		= array("value" => "left",  "inherit" => false, "html" => false);
		$this->property["labelwidth"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelstyle"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["calendar"]      = array("value" => null, "inherit" => false, "html" => false);
		$this->property["csscalendar"]   = array("value" => null, "inherit" => false, "html" => false);
		$this->property["classcalendar"] = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["format"] 			= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["java"]  			= array("value" => array("text.js", "regexp.js", "format.js"), "inherit" => false, "html" => false);
 		$this->property["cssfile"]  		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsobj"]  			= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]  		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["disabled"]  		= array("value" => false, "inherit" => false, "html" => false);
		$this->property["readonly"]  		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["dsobjlist"] 		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["dsitemlist"]   	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["dssearch"]   	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["minsearch"]   	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["savesearch"]   	= array("value" => "false",  "inherit" => false, "html" => false);

		$this->property["forcename"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dimension"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["createdir"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["backgroundcolor"] = array("value" => null, "inherit" => false, "html" => false);		
		
		$this->property["width"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["height"]	= array("value" => null, "inherit" => false, "html" => false);

		// ATTRIBUTE FOR HTML 5
		$this->property["autocomplete"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["autofocus"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["form"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["formaction"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["formenctype"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["formmethod"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["formnovalidate"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["formtarget"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["list"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["max"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["min"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["multiple"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["pattern"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["placeholder"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["required"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["step"]	= array("value" => null, "inherit" => false, "html" => true);
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
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		global $system;
		$code = "";
		$id = $this->property["id"]["value"];
		$readonly = ($this->property["readonly"]["value"] == "true") ? " readonly" : "";
		$disabled = ($this->property["disabled"]["value"] == "true") ? " disabled" : "";
		$type = ($this->property["password"]["value"] == "true") ? "password" : "text";
		if ($this->property["fileupload"]["value"] == "true")
		{
			$filestore = defined("FILESTORE") ? FILESTORE : $system->dir_web_jamp.$system->dir_class."filestore.php";
			if (empty($this->property["target"]["value"]))
			{
				$code .= "\n$tab<form action=\"".$filestore."\" method=\"post\" enctype=\"multipart/form-data\" target=\"".$id."_target\">";
				$code .= "\n$tab\t<iframe style=\"display:none\" name=\"".$id."_target\"></iframe>";
			}
			else $code .= "\n$tab<form action=\"".$filestore."\" method=\"post\" enctype=\"multipart/form-data\" target=\"".$this->property["target"]["value"]."\">";
			$code .= "\n$tab\t<input type=\"hidden\" name=\"directory\" value=\"".$this->property["directory"]["value"]."\">";
			$code .= "\n$tab\t<input type=\"hidden\" name=\"classname\" value=\"TEXT\">";
			$code .= "\n$tab\t<input type=\"hidden\" name=\"forcename\" value=\"".$this->property["forcename"]["value"]."\">";
			$code .= "\n$tab\t<input type=\"hidden\" name=\"rewrite\" value=\"".$this->property["rewrite"]["value"]."\">";
			$code .= "\n$tab\t<input type=\"hidden\" name=\"createdir\" value=\"".$this->property["createdir"]["value"]."\">";
			$code .= "\n$tab\t<input type=\"hidden\" name=\"backgroundcolor\" value=\"".$this->property["backgroundcolor"]["value"]."\">";
			$code .= "\n$tab\t<input type=\"text\" ".$this->getProperty("html", true, false)."$disabled>";
			$code .= "\n$tab\t<input style=\"width: 50px;position: relative;filter:alpha(opacity: 0);opacity: 0;z-index: 10\" type=\"file\" id=\"".$id."_file\" name=\"$id\""." size=\"1\" onchange=\"TEXT.postObj('$id');\" $disabled>";
			$template = empty($this->property["template"]["value"]) ? "default" : $this->property["template"]["value"];
			$code .= "\n$tab\t<image style=\"position: relative;top: 5px; left: -50px\" src=\"".$system->dir_web_jamp.$system->dir_template."objcss/$template/page/search.gif\">";
			$code .= "\n$tab\t<select id=\"".$id."_dimension\" name=\"dimension[]\" multiple=\"true\" style=\"display: none\"></select>";
			$code .= "\n$tab</form>";
		} 
		else $code .= "\n$tab<input type=\"$type\" ".$this->getProperty("html", true, false).$readonly.$disabled.">";
		if (!empty($this->property["dsobjlist"]["value"])) $code .= "\n$tab<div class=\"autocomplete\" style=\"overflow:auto;display:none;z-index: 99999\"></div>";

		$code = $this->getCodeLabelAlign($code, $tab);
		return $code;
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
			$this->addEvent($id, $dsobj."Move", "TEXT.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "TEXT.refreshObj(\"$id\");");
			if ($this->property["readonly"]["value"]!="true")
			{
				$this->property["onchange"]["value"] = "TEXT.setDsValue(this); ".$this->property["onchange"]["value"];
				$this->property["onkeyup"]["value"] = "TEXT.checkDsValue(this); ".$this->property["onkeyup"]["value"];
			}
		}
		else if (!empty($this->property["format"]["value"]))
		{
			global $system;
			require_once($system->dir_real_jamp."/class/format.class.php");
			$format = new ClsFormat();
			$this->property["value"]["value"] = $format->Format($this->property["value"]["value"], $this->property["format"]["value"]);
			$this->property["onblur"]["value"] = "FORMAT.format(this, this.value); ".$this->property["onblur"]["value"];
		}
		if ($this->property["readonly"]["value"]!="true")
		{
			if (substr($this->property["format"]["value"],0,4)=="date" && $this->property["calendar"]["value"] != "false")
			{
				$this->property["java"]["value"][] = "calendar.js";
				$template = empty($this->property["template"]["value"]) ? "default" : $this->property["template"]["value"];
				$this->propertyJS["classcalendar"] = "calendar_$template";
				$this->property["cssfile"]["value"] = "objcss/$template/calendar.css";
				$this->property["onclick"]["value"] = "CALENDAR.show_picker(this); ".$this->property["onclick"]["value"];
			}
		}
		if (!empty($this->property["keypress"]["value"]))
		{
			$keypress = $this->property["keypress"]["value"];
			$this->property["onkeypress"]["value"] = "return REGEXP.checkDigit(event,'".$keypress."'); ".$this->property["onkeypress"]["value"];
		}
		if (!empty($this->property["minlength"]["value"]) || !empty($this->property["blur"]["value"]))
		{
			$blur = $this->property["blur"]["value"];
			$this->property["onblur"]["value"] = "return REGEXP.checkWord(this, '$blur'); ".$this->property["onblur"]["value"];
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
			case "directory":
				if (substr($this->property["directory"]["value"],0,2) == "./")
				{
					$this->property["directory"]["value"] = substr($this->property["directory"]["value"],2);
					$this->property["directory"]["value"] = dirname($_SERVER['PHP_SELF'])."/".$this->property["directory"]["value"];
				}
			break;
			case "minsearch":
				$this->propertyJS[$name] = intval($this->property[$name]["value"]);
			break;	
			case "savesearch":
				if ($this->property[$name]["value"] == "true") $this->propertyJS[$name] = true;
				else $this->propertyJS[$name] = false;
			break;	
			case "minlength":
			case "format":
			case "defaultvalue":
			case "classcalendar":
			case "csscalendar":
			case "dimension":
			case "send":
				$this->propertyJS[$name] = $this->property[$name]["value"];
			break;	
			case "readonly":
				if ($this->property["readonly"]["value"] == "false") $this->property["readonly"]["value"] = null;
			break;
			case "dsitemlist":
				$this->propertyJS["dsItemList"] = $this->property["dsitemlist"]["value"];
			break;
			case "dsobjlist":
				$this->propertyJS["dsObjList"] = $this->property["dsobjlist"]["value"];
 				$this->property["onkeyup"]["value"] = "TEXT.AutoComplete(event, this); ".$this->property["onkeyup"]["value"];
 				$this->property["onblur"]["value"] = "TEXT.lostFocus(event, this);".$this->property["onblur"]["value"];
			break;
			case "dssearch":
				$this->propertyJS["dsSearch"] = $this->property["dssearch"]["value"];
			break;
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

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "TEXT.refreshObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>
