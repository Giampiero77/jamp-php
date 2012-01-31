<?php
/**
* Object DSSELECT
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_dsselect extends ClsObject {
	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["name"] 	   	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["value"] 	   	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["minlength"] 	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["blur"] 		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["size"] 	   	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["tabindex"]   	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onfocus"]    	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onselect"]   	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onblur"] 	   	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onchange"]   	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["id"] 		   	= array("value" => $id,   "inherit" => false, "html" => true);
		$this->property["dsitemkeylist"]= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["nav"] 		   	= array("value" => false, "inherit" => false, "html" => false);
		$this->property["memory"] 	   	= array("value" => false, "inherit" => false, "html" => false);
		$this->property["multiple"]   	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["dsitemlist"]  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["dsobjlist"]  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["directory"]  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["allselect"]  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["valuezero"]  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["label"] 	   	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["labelalign"]	= array("value" => "left",  "inherit" => false, "html" => false);
		$this->property["labelwidth"] 	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["labelstyle"] 	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["outlabel"]		= array("value" => "false", "inherit" => false, "html" => false);
		$this->property["optionvalue"] 	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["optiontext"]  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["optionselected"] = array("value" => null,  "inherit" => false, "html" => false);
		$this->property["optionimage"] 	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["width"]	  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["height"]	  	= array("value" => null,  "inherit" => false, "html" => false);
 		$this->property["java"]  		= array("value" => array("dsselect.js", "format.js"), "inherit" => false, "html" => false);
		$this->property["dsobj"]  		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsitem"]  		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["disabled"] 	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["format"] 		= array("value" => null,  "inherit" => false, "html" => false);
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
		if (!empty($this->property["value"]["value"]))
		{
			if (!empty($this->property["dsobjlist"]["value"]))
			{
				global $xml;
				$dsObj = $xml->getObjById($this->property["dsobjlist"]["value"]);
				$key = $this->property["dsitemkeylist"]["value"];
				$value = $this->property["dsitemlist"]["value"];
				if ($dsObj->ds->dsCountRow() > 0)
				{
						$row = $dsObj->ds->dsGetRow(0);
						do
						{
							 if ($row->$key == $this->property["value"]["value"]) 
							 {
								  $this->property["value"]["value"] = $row->$value;
								  break;
							 }
						}
						while ($row = $dsObj->ds->dsGetRow());
				}	
			}
			else if (!empty($this->property["optionvalue"]["value"]))
			{
				if (!empty($this->property["optiontext"]["value"]))
				{
					if (!is_array($this->property["optionvalue"]["value"])) $this->property["optionvalue"]["value"] = explode(",",$this->property["optionvalue"]["value"]);
					if (!is_array($this->property["optiontext"]["value"])) $this->property["optiontext"]["value"] = explode(",",$this->property["optiontext"]["value"]);
					$option = array_combine($this->property["optionvalue"]["value"], $this->property["optiontext"]["value"]);
					$this->property["value"]["value"] = $option[$this->property["value"]["value"]];
				}
			}
		}
		else $this->property["value"]["value"] = "";
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
		$code = "";
		if (!empty($this->property["width"]["value"])) $this->property["style"]["value"] .= "width:".$this->property["width"]["value"].";";
		if (!empty($this->property["height"]["value"])) $this->property["style"]["value"] .= "height:".$this->property["height"]["value"].";";
		if ($this->property["outlabel"]["value"] == "true") $this->property["style"]["value"] .= "display:none;";
		if (isset($this->property["directory"]["value"])) $this->property["class"]["value"] = "selectImage";
		$code .= "\n$tab<select ".$this->getProperty("html", true, false);
		if ($this->property["disabled"]["value"] == "true") $code .= " disabled ";
		$code .= ">";
		if (!empty($this->property["valuezero"]["value"])) $code .= "<option value=\"0\">".$this->property["valuezero"]["value"]."</option>";
		if (!empty($this->property["optionvalue"]["value"])) 
		{
			if (!is_array($this->property["optionvalue"]["value"])) $this->property["optionvalue"]["value"] = explode(",",$this->property["optionvalue"]["value"]);
			if (!is_array($this->property["optiontext"]["value"])) $this->property["optiontext"]["value"] = explode(",",$this->property["optiontext"]["value"]);
			if (!is_array($this->property["optionselected"]["value"])) $this->property["optionselected"]["value"] = explode(",",$this->property["optionselected"]["value"]);
			if (isset($this->property["directory"]["value"])) 
			{
				$directory = $this->property["directory"]["value"];
				$class = $this->property["class"]["value"];
				foreach($this->property["optionvalue"]["value"] as $k => $value)
				{
					$text = $this->property["optiontext"]["value"][$k];
					$img =  $this->property["optionimage"]["value"][$k];
					$url = "background url('".$directory."/".$img."')";
					$selected = "";
					if (isset($this->property["optionselected"]["value"][$k]) && $this->property["optionselected"]["value"][$k] == "true")
					{
						$selected=" selected";
						$this->property["value"]["value"] = $value;
					}
					$code .= "\n$tab\t<option class=\"$class\" style=\"$url\" value=\"$value\"$selected>$text</OPTION>";
				}
			}
			else
			{
				if (!empty($this->property["format"]["value"]))
				{
					global $system;
					require_once($system->dir_real_jamp."/class/format.class.php");
					$format = new ClsFormat();
				}
				foreach($this->property["optionvalue"]["value"] as $k => $value)
				{
					$text = $value;
 					if (!empty($this->property["optiontext"]["value"][$k])) $text = $this->property["optiontext"]["value"][$k];
					$selected = "";
					if ((isset($this->property["optionselected"]["value"][$k]) && $this->property["optionselected"]["value"][$k] == "true") ||
					($this->property["value"]["value"]==$value))
					{
						$selected=" selected";
						$this->property["value"]["value"] = $value;
					}
					if (!empty($this->property["format"]["value"])) $text = $format->Format($text, $this->property["format"]["value"]);
					$code .= "\n$tab\t<option value=\"$value\"$selected>$text</option>";
				}
			}
		}
		$code .= "\n$tab</select>";
		if ($this->property["outlabel"]["value"] == "true") $code .= "<span name=\"dsselect_label\"></span>";
		$code = $this->getCodeLabelAlign($code, $tab);
		return $code;
	}
	
	/**
	* Builds the object
	*/
	public function BuildObj()	
	{
		$id = $this->property["id"]["value"];
		if (!empty($this->property["dsobjlist"]["value"]))
		{
			$dsobjlist = $this->property["dsobjlist"]["value"];
			$this->propertyJS["dsObjList"] = $dsobjlist;
			$this->addEvent($id, $dsobjlist."Refresh", "DSSELECT.refreshObj(\"$id\");");
		}
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
			$this->propertyJS["dsObj"] = $dsobj;
			$this->addEvent($id, $dsobj."Move", "DSSELECT.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "DSSELECT.getDsValue(\"$id\");");
		}
		if (!empty($this->property["minlength"]["value"]) || !empty($this->property["blur"]["value"]))
		{
			$blur = $this->property["blur"]["value"];
			$this->property["onblur"]["value"] = "return REGEXP.checkWord(this, '$blur'); ".$this->property["onblur"]["value"];
		}
		$this->property["onchange"]["value"] = "DSSELECT.change(this); ".$this->property["onchange"]["value"];
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
			case "dsitemkeylist":
				$this->propertyJS["dsItemKeyList"] = $this->property["dsitemkeylist"]["value"];
			break;

			case "dsitemlist":
				$this->propertyJS["dsItemList"] = $this->property["dsitemlist"]["value"];
			break;

			case "value":
				$this->propertyJS["value"] = $this->property["value"]["value"];
			break;

			case "nav":
				if ($this->property["nav"]["value"] == true) $this->propertyJS["dsNav"] = true;
			break;

			case "height":
				if ($this->property["height"]["value"] == "autosize")
				{
					$this->property["height"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
				}
			break;

			case "width":
				if ($this->property["width"]["value"] == "autosize")
				{
					$this->property["width"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
				}
			break;

			case "directory":
			case "minlength":
			case "allselect":
			case "memory":
			case "valuezero":
			case "format":
			case "disabled":
				$this->propertyJS[$name] = $this->property[$name]["value"];
			break;

			case "outlabel":
				if ($this->property["outlabel"]["value"] == "true") $this->propertyJS["outlabel"] = true;
			break;

			case "optionvalue":
				$this->propertyJS["customvalue"] = true;
			break;
		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		$js = "";
		if (!empty($this->property["dsobjlist"]["value"]) || !empty($this->property["dsobj"]["value"]))
		{
			$js = "DSSELECT.refreshObj(\"".$this->property["id"]["value"]."\");";
		}
		return $js;
	}
}
?>