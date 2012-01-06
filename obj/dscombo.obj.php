<?php
/**
* Object DSCOMBO
* @author	Alyx Association <info@alyx.it>
* @version	1.0.1 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_dscombo extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"] 		  	  	= array("value" => $id,   "inherit" => false, "html" => true);
		$this->property["keypress"]  	  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["blur"]  	  	  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["minlength"] 	  	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["value"] 	  	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["name"] 	  	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["size"] 	  	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["maxlength"] 	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["alt"] 		  	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["src"] 		  	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["readonly"]  	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["align"] 	  	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["accesskey"] 	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onfocus"]   	  	= array("value" => "this.select()",  "inherit" => false, "html" => true);
		$this->property["onselect"]  	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onblur"] 	  	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onchange"]  	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["tabindex"]  	  	= array("value" => null,  "inherit" => false, "html" => true);
 		$this->property["dsobj"]  			= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]  			= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsobjlist"] 		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["dsitemlabel"]		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["dsitemlist"]   	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["dsitemkeylist"]	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["label"] 	     	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["labelalign"]		= array("value" => "left",  "inherit" => false, "html" => false);
		$this->property["labelwidth"]   	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["labelstyle"]   	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["dssearch"]     	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["outlabel"]			= array("value" => "false", "inherit" => false, "html" => false);
 		$this->property["java"]  			= array("value" => array("dscombo.js", "format.js"), "inherit" => false, "html" => false);
 		$this->property["cssfile"]  		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["format"] 			= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["title"]["value"] 	= LANG::translate("DSCOMB001");
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
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
				$option = array_combine($this->property["optionvalue"]["value"], $this->property["optiontext"]["value"]);
				$this->property["value"]["value"] = $option[$this->property["value"]["value"]];
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
		global $system;
		if ($this->property["outlabel"]["value"] == "true") $code = "\n$tab<span id=\"".$this->property["id"]["value"]."\"></span>";
		else 
		{
			$code = "\n$tab<input type = \"text\" ".$this->getProperty("html", true, false).">";
			$code .= "\n$tab<span class=\"".$this->property["class"]["value"]."_expand\" onclick=\"DSCOMBO.expand(this);\">&nbsp</span>";
			$code .= "\n$tab<div class=\"".$this->property["class"]["value"]."_result\" style=\"overflow:auto;display:none;\"></div>";
		}
		$code = $this->getCodeLabelAlign($code, $tab);
		return $code;
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$id = $this->property["id"]["value"];
		$this->setCSS();
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
			if (empty($this->property["dsitemlabel"]["value"]))
			{
				$this->addEvent($id, $dsobj."Move", "DSCOMBO.getDsValue(\"$id\");");
				$this->addEvent($id, $dsobj."Refresh", "DSCOMBO.getDsValue(\"$id\");");
			}
			else
			{
				$this->addEvent($id, $dsobj."Move", "DSCOMBO.getDsValueLabel(\"$id\");");
				$this->addEvent($id, $dsobj."Refresh", "DSCOMBO.getDsValueLabel(\"$id\");");
			}
		}
		if (!empty($this->property["minlength"]["value"]) || !empty($this->property["blur"]["value"]))
		{
			$blur = $this->property["blur"]["value"];
			$this->property["onblur"]["value"] = "return REGEXP.checkWord(this, '".$type."'); ".$this->property["onblur"]["value"];
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
			case "keypress":
				$keypress = $this->property["keypress"]["value"];
				$this->property["onkeypress"]["value"] = "return REGEXP.checkDigit(event,'".$type."'); ".$this->property["onkeypress"]["value"];
			break;		
		
			case "minlength":
			case "format":
				$this->propertyJS[$name] = $this->property[$name]["value"];
			break;	

			case "outlabel":
				if ($this->property["outlabel"]["value"] == "true") $this->propertyJS["outlabel"] = true;
			break;	

			case "dsitemkeylist":
				$this->propertyJS["dsItemKeyList"] = $this->property["dsitemkeylist"]["value"];
			break;

			case "dssearch":
				$this->propertyJS["dsSearch"] = $this->property["dssearch"]["value"];
			break;

			case "dsitemlist":
				$this->propertyJS["dsItemList"] = $this->property["dsitemlist"]["value"];
			break;

			case "dsitemlabel":
				$this->propertyJS["dsItemLabel"] = $this->property["dsitemlabel"]["value"];
			break;

			case "dsobjlist":
				$this->propertyJS["dsObjList"] = $this->property["dsobjlist"]["value"];
 				$this->property["onkeyup"]["value"] = "DSCOMBO.delaySearch(event, this); ".$this->property["onkeyup"]["value"];
			break;
		}
	}
}
?>
