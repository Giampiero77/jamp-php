<?php
/**
* Object TEXTAREA
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_textarea extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id) 
	{
		$this->property["id"] 		  	= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["name"] 	  	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["cols"] 	 	= array("value" => "30", "inherit" => false, "html" => true);
		$this->property["rows"]	 		= array("value" => "5", "inherit" => false, "html" => true);
		$this->property["alt"] 		 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["src"] 		  	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["accesskey"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onfocus"]   	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onselect"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onblur"] 	  	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onchange"]  	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["tabindex"]  	= array("value" => null, "inherit" => false, "html" => true);

		$this->property["value"] 	 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["defaultvalue"]	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["width"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["height"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["label"] 	  	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelalign"]	= array("value" => "left",  "inherit" => false, "html" => false);
		$this->property["labelwidth"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelstyle"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["java"]			= array("value" => array("textarea.js", "regexp.js", "format.js"), "inherit" => false, "html" => false);
 		$this->property["dsobj"]  		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]  		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["display"]   	= array("value" => null, "inherit" => true, "html" => false);
		$this->property["keypress"]  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["blur"]  	  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["send"]  	  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["format"]  	  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["minlength"]  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["maxlength"]  	= array("value" => null,  "inherit" => false, "html" => false);

		$this->property["disabled"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["readonly"] 	= array("value" => null, "inherit" => false, "html" => false);

		//Plugin
		$this->property["editor-web"] 	= array("value" => null,   "inherit" => false, "html" => false);
		$this->property["w3c"] 				= array("value" => null, "inherit" => false, "html" => false);

		$this->property["editor-file"]	= array("value" => null,   "inherit" => false, "html" => false);
		$this->property["editor-lang"] 	= array("value" => null,   "inherit" => false, "html" => false);
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
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$id = $this->property["id"]["value"];

		if (!empty($this->property["width"]["value"])) $this->property["style"]["value"] .= "width:".$this->property["width"]["value"].";";
		if (!empty($this->property["height"]["value"])) $this->property["style"]["value"] .= "height:".$this->property["height"]["value"].";";

		//Codepress
		if (!empty($this->property["editor-lang"]["value"])) 
		{
			global $system;
			$img = $system->dir_web_jamp.$system->dir_plugin."codepress/images";
		}

		$readonly = ($this->property["readonly"]["value"] == "true") ? " readonly" : "";
		$disabled = ($this->property["disabled"]["value"] == "true") ? " disabled" : "";

		$code = "\n$tab<textarea ".$this->getProperty("html", true, false).$readonly.$disabled.">";
		if (!empty($this->property["value"]["value"])) $code .= htmlspecialchars(str_replace('\n',"<br>",$this->property["value"]["value"]));
		if (!empty($this->property["editor-file"]["value"])) 
		{
			$file = file($this->property["editor-file"]["value"]);
			foreach ($file as $row) $code .= htmlspecialchars($row);
		}
		$code .= "</textarea>";
		if (!empty($this->property["editor-lang"]["value"])) $this->property["id"]["value"] .= "_code";
		$code = $this->getCodeLabelAlign($code, $tab);    
		$this->property["id"]["value"] = $id;
		return $code;
	}

	/**
	* Builds the object
	*/
	function BuildObj()
	{
		$this->codejs = "";
		$id = $this->property["id"]["value"];
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
			$this->addEvent($id, $dsobj."Move", "TEXTAREA.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "TEXTAREA.refreshObj(\"$id\");");
			$this->property["onchange"]["value"] = "TEXTAREA.setDsValue(this); ".$this->property["onchange"]["value"];
			$this->property["onkeyup"]["value"] = "TEXTAREA.checkDsValue(this); ".$this->property["onkeyup"]["value"];
		}

		if ($this->property["editor-web"]["value"] == true) 
		{
			$this->property["editor-lang"]["value"] = "";
			$this->property["java"]["value"][] = "../plugin/tinymce/jscripts/tiny_mce/tiny_mce.js";

			$id = $this->property["id"]["value"];
			$setting = $this->property["editor-web"]["value"];

			$this->addEvent($id, $this->property["dsobj"]["value"]."Move", "TEXTAREA.getTextArea('$id');");
			$this->addEvent($id, $this->property["dsobj"]["value"]."Refresh", "TEXTAREA.getTextArea('$id');");
			$this->addEvent($id, $this->property["dsobj"]["value"]."BeforeSave", "TEXTAREA.updateTextArea('$id');");

			$readonly = ($this->property["readonly"]["value"] == "true") ? true : false;
			if (empty($this->property["display"]["value"])) $this->addEventListener("window", "load", "function() { TEXTAREA.initEditor('$id', '$readonly'); }");
			else $this->addEventBefore($id, $this->property["display"]["value"]."Display", "TEXTAREA.initEditor('$id', '$readonly');");
		}

		$id = (empty($this->property["editor-lang"]["value"])) ? $this->property["id"]["value"] : $this->property["id"]["value"]."_code";
		$autosizeH = $autosizeW = "false";
		if ($this->property["height"]["value"] == "autosize")
		{
			$this->property["height"]["value"] = "0";
			$this->addEventListener("window", "resize", "Resize");
			$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
			$autosizeH = "true";
		}

		if ($this->property["width"]["value"] == "autosize")
		{
			$this->property["width"]["value"] = "0";
			$this->addEventListener("window", "resize", "Resize");
			$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
			$autosizeW = "true";
		}

		//Codepress
		if (!empty($this->property["editor-lang"]["value"]))
		{
			$id = $this->property["id"]["value"];
			$idcp = $id."_cp";
			$this->property["java"]["value"][] = "../plugin/codepress/codepress.js";
			$this->property["class"]["value"] = "codepress ".$this->property["editor-lang"]["value"];
 			if (empty($this->property["display"]["value"])) $this->setCodeJs("$idcp.edit();");
 			else $this->addEventBefore($id, $this->property["display"]["value"]."Display", "$idcp.displayObj($autosizeH, $autosizeW);");

			if (!empty($this->property["dsobj"]["value"]))
			{
				$this->addEvent($id, $this->property["dsobj"]["value"]."Move", "$idcp.setCode($idcp.textarea.value);");
				$this->addEvent($id, $this->property["dsobj"]["value"]."Move", "$idcp.editor.syntaxHighlight('init');");
				$this->addEvent($id, $this->property["dsobj"]["value"]."Refresh", "if ($idcp.editor != undefined) $idcp.setCode($idcp.textarea.value);");
				$this->addEvent($id, $this->property["dsobj"]["value"]."Refresh", "if ($idcp.editor != undefined) $idcp.editor.syntaxHighlight('init');");
				$this->addEvent($id, $this->property["dsobj"]["value"]."BeforeSave", "$idcp.setTextValue();");
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
		if (!empty($this->property["maxlength"]["value"]))
		{		
			$this->property["onkeyup"]["value"] = "return REGEXP.doKeyUp(this); ".$this->property["onkeyup"]["value"];
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
			case "minlength":
			case "maxlength":
			case "format":
			case "value":
			case "w3c":
			case "defaultvalue":
			case "send":		  
				$this->propertyJS[$name] = $this->property[$name]["value"];
			break;	
			case "readonly":
				if ($this->property["readonly"]["value"] == "false") $this->property["readonly"]["value"] = null;
			break;
		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "TEXTAREA.refreshObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>
