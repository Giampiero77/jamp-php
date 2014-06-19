<?php
/**
* Object IMAGE
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_image extends ClsObject {
	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		global $system;
		$this->property["class"] 	  	= array("value" => "image", "inherit" => false, "html" => true);
		$this->property["readonly"]  	= array("value" => "false", "inherit" => false, "html" => false);
		$this->property["mouseup"]   	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["id"] 		 	= array("value" => $id,  "inherit" => false, "html" => true);		
		$this->property["maxsize"]   	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["alt"] 		 	= array("value" => "", "inherit" => false, "html" => true);
		$this->property["height"] 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["src"]			= array("value" => null, "inherit" => false, "html" => true);
		$this->property["hspace"]    	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["ismap"] 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["longdesc"]  	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["usemap"] 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["vspace"] 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["width"] 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["accesskey"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onfocus"]   	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onselect"]  	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onblur"] 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onchange"]  	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["tabindex"]  	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["value"]  	 	= array("value" => null, "inherit" => false, "html" => false);
 		$dir = dirname($_SERVER['PHP_SELF'])."/";
		$this->property["directory"] 	= array("value" => $dir, "inherit" => false, "html" => false);
		$this->property["extension"] 	= array("value" => "image", "inherit" => false, "html" => false);
		$this->property["border"] 	 	= array("value" => "1", "inherit" => false, "html" => true);
		$this->property["label"] 	 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelalign"]	= array("value" => "left",  "inherit" => false, "html" => false);
		$this->property["labelwidth"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelstyle"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["java"] 		= array("value" => "image.js", "inherit" => false, "html" => false);
		$this->property["cssfile"] 		= array("value" => "objcss/default/image.css", "inherit" => false, "html" => false);
 		$this->property["dsobj"]  		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]  		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["display"]   	= array("value" => null, "inherit" => true, "html" => false);
		$this->property["size"]			= array("value" => null, "inherit" => false, "html" => false);

		$this->property["fixed"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dimension"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["forcename"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["createdir"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["backgroundcolor"] = array("value" => null, "inherit" => false, "html" => false);

		$this->property["emptyimage"] = array("value" => null, "inherit" => false, "html" => false);

		$this->realpath = false;
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		global $system;
		$id = $this->property["id"]["value"];
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
 			$this->addEvent($id, $dsobj."Move", "IMAGE.getDsValue(\"$id\");");
 			$this->addEvent($id, $dsobj."Refresh", "IMAGE.refreshObj(\"$id\");");
		}
		if (!empty($this->property["display"]["value"])) $this->addEventBefore($id, $this->property["display"]["value"]."Display", "IMAGE.displayObj(\"$id\");");
		if ($this->property["fixed"]["value"]=="true")	$this->propertyJS["fixed"] = true;
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		global $xml, $system;
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsObj = $xml->getObjById($this->property["dsobj"]["value"]);
			$row = $dsObj->ds->dsGetRow(0);
			$item = $this->property["dsitem"]["value"];
			$this->property["value"]["value"] = $row->$item;
		}
		if (!empty($this->property["value"]["value"])) 
			$this->property["src"]["value"] = $this->property["directory"]["value"].$this->property["value"]["value"];
		else $this->property["src"]["value"] = $system->dir_real_jamp."/".$system->dir_template."objcss/default/image/none.gif";
		
		$this->property["value"]["value"] = "";
		if ($this->realpath==true) $this->property["src"]["value"] = getenv("DOCUMENT_ROOT").$this->property["src"]["value"];
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
		global $system;
		$id = $this->property["id"]["value"];
		$phpstore = defined("FILESTORE") ? FILESTORE : $system->dir_web_jamp.$system->dir_class."filestore.php";

		$this->propertyJS["action"] = $phpstore;
		$this->propertyJS["directory"] = $this->property["directory"]["value"];
		$this->propertyJS["extension"] = $this->property["extension"]["value"];
		$this->propertyJS["empty"] = $this->getEmptyImage();
		if ($this->property["fixed"]["value"]=="true") $style="width:".$this->property["width"]["value"]."; height:".$this->property["height"]["value"].";";
		else $style = "width:0px; height:0px;";
 		$code = "\n$tab<div class=\"".$this->property["class"]["value"]."\" style=\"$style\">";
 		if ($this->property["readonly"]["value"]!="true") 
 		{
			$size = str_ireplace("[[:alpha:]]", "", $this->property["width"]["value"]);
			$size = intval($size / 10);
 			$size = ($size < 10) ? 5 : $size;
 			$code .= "$tab\t<input id=\"".$id."_file\" class=\"".$this->property["class"]["value"]."\" type=\"file\" name=\"uploadfile\"  size=\"".$size."\">\n";
 			$code .= "$tab\t<div id=\"".$id."_add\"><div></div></div>";
			$code .= "\n\t$tab<img ".$this->getProperty("html", true, false)." onmouseup=\"IMAGE.moveRow(this);\">\n";
  		}	
		else $code .= "\n\t$tab<img ".$this->getProperty("html", true, false)." onmouseup=\"IMAGE.moveRow(this);\">\n";
		$code .= "$tab</div>\n";
		$code = $this->getCodeLabelAlign($code, $tab);
		return $code;
	}
	
	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		global $system;
		$id = $this->property["id"]["value"];
		switch($name)
		{
			case "directory":
				if (substr($this->property["directory"]["value"],0,2) == "./")
				{
					$this->realpath = true;
					$this->property["directory"]["value"] = substr($this->property["directory"]["value"],2);
					$this->property["directory"]["value"] = dirname($_SERVER['PHP_SELF'])."/".$this->property["directory"]["value"];
				}
			break;
			case "maxsize":
			case "dimension":
			case "forcename":
			case "createdir":
			case "backgroundcolor":
				$this->propertyJS[$name] = $this->property[$name]["value"];
			break;	
		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "IMAGE.refreshObj(\"".$this->property["id"]["value"]."\");";
	}

	protected function getEmptyImage() {
		global $system;

		$img = $this->property["emptyimage"]["value"];
		if (empty($img)) {
			return $system->dir_web_jamp.$system->dir_template."objcss/default/image/none.gif";
		} else {
			return $this->property["directory"]["value"].$img;
		}
	}
}
?>
