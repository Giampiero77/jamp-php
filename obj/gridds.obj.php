<?php
/**
* Object GRIDDS
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_gridds extends ClsObject {
	/**
	* @var $container Array containing the child objects
	*/
	var $child = array();

	/**
	* @var $child_property Properties of child objects
	*/
	private $child_property;

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"] 			= array("value" => $id, "inherit" => false, "html" => true);
		$this->property["label"]   		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["order"]   		= array("value" => "false",	"inherit" => false, "html" => false);
		$this->property["readonly"] 	= array("value" => "true", "inherit" => false, "html" => false);
		$this->property["insertnew"] 	= array("value" => "true", "inherit" => false, "html" => false);
		$this->property["width"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["height"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["cssfile"] 		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["class"] 	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["display"]   	= array("value" => null, "inherit" => true, "html" => false);
 		$this->property["java"]  	  	= array("value" => "gridds.js", "inherit" => false, "html" => false);
		$this->property["pdffont"]	  	= array("value" => "Arial", "inherit" => false, "html" => false);
		$this->property["pdffontsize"]	= array("value" => "8", "inherit" => false, "html" => false);
 		$this->property["dsobj"]  		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["tabchar"]  	= array("value" => "9", "inherit" => false, "html" => false);
 		$this->property["autoscroll"]	= array("value" => "true", "inherit" => false, "html" => false);
		$this->multiObj = true;
	}

	/**
	* Return a Property Child
	* @param integer $child Child's number
	*/
	public function getPropertyChild()
	{
		return $this->child_property;
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	public function setProperty($name, $value)
	{
		if (is_array($value)) $this->child_property[$name] = $value;
		else parent::setProperty($name, $value);
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$this->codejs = "";
		$id = $this->property["id"]["value"];
		$dsobj = $this->property["dsobj"]["value"];
		$class = $this->setCSS();
		$this->propertyJS["insertNew"] = false;
		if ($this->property["insertnew"]["value"] == "true") $this->propertyJS["insertNew"] = true;	

		$this->addEventAfter($id, $dsobj."Refresh", "GRIDDS.refreshObj('$id');");
 		$this->addEventAfter($id, $dsobj."Move", "GRIDDS.getDSpos($('$id'));");
		$this->addEventBefore($id, $dsobj."SaveRow", "GRIDDS.saveRow('$id');");

  		$this->addEventListener($id."_body", "keydown", "function(event) { GRIDDS.keyDown('$id', event); } ");
  		$this->addEventListener($id."_body", "keyup", "function(event) { GRIDDS.keyUp('$id', event); } ");
  		$this->addEventListener($id."_body", "click", "function() { GRIDDS.setFocus('$id'); } ");
  		
  		if(($this->property["tabchar"]["value"] != "9") && !empty($this->property["tabchar"]["value"])) $this->addEventListener($id."_body", "keyup", "function(event) { GRIDDS.tab('$id', ".$this->property["tabchar"]["value"].", event); } ");
 
		//COLS
		$ObjROW = $this->addChild($id."_row0", "div");
		$ObjROW->setProperty("class", $class."_row0");
		$ObjROW->setProperty("onclick", "GRIDDS.clickROW($('$id'), this.row, event);");
		$i = 0;
		if (isset($this->child_property["objtype"]))
		{
			foreach ($this->child_property["objtype"] as $k => $type)
			{
				$i++;
				$ObjCOL = $ObjROW->addChild($id."_row0"."_col$i", "div", true);
				$ObjCOL->setProperty("class", $class."_col");
	
				$ObjItem = $ObjCOL->addChild($id."_".$i."_0", $type, true); //Object
				if (isset($this->child_property["dsitem"][$i-1])) 
				{
					if ($ObjItem->issetProperty("dsobj"))
					{
						if (isset($this->child_property["colalign"][$k])) $ObjItem->setProperty("style", "text-align:".$this->child_property["colalign"][$k].";");
						$ObjItem->setProperty("dsobj", $dsobj);
						$ObjItem->setProperty("dsitem", $this->child_property["dsitem"][$i-1], false);
					}
				}
				foreach ($ObjItem->enumProperty(true) as $name)
				{
					if (isset($this->child_property[$name][$i-1]) && is_array($this->child_property[$name]))
							$ObjItem->setProperty($name, $this->child_property[$name][$i-1]);
				}
				$ObjItem->BuildObj();
				$ObjItem->removeAllEvent();
			}
		}

		if (empty($this->property["display"]["value"])) $this->setCodeJs("\t\tGRIDDS.initObj(\"".$id."\");");
		else $this->addEventBefore($id, $this->property["display"]["value"]."Display", "GRIDDS.displayObj(\"$id\");");
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		$pdf->headrow["data"] = null;
		$pdf->headrow["row"] = 1;
		$pdf->Ln();
		global $xml;
		$id = $this->property["id"]["value"];
		$dsObj = $xml->getObjById($this->property["dsobj"]["value"]);
		$this->child = "";
		$tableOBJ = $this->addChild($id."_table","table");

		//Header
		$headObj = $tableOBJ->addChild($id."_thead", "thead", true);
		if (isset($this->child_property["headlabel"]))
		{
			$ObjTR = $headObj->addChild($id."_head", "tr", true);
			$i = 0;
			$istart = 1;
			foreach ($this->child_property["headlabel"] as $col)
			{
				$colspan = $this->child_property["headcol"][$i];
				$i++;
				$ObjTH = $ObjTR->addChild($id."_head$i", "th", true);
				$ObjTH->setProperty("nowrap", "nowrap");
				$ObjTH->setProperty("colspan", $colspan);
				if ($colspan==1) $ObjTH->setProperty("class", "gridds_headnone");
	
				$ObjDIV = $ObjTH->addChild($id."_head$i"."_label", "div", true);
				$ObjDIV->setProperty("value", $col);
				$size = 0;
				for($z = $istart; $z < $istart + $colspan; $z++)
				{
					if (!empty($this->child_property["size"][$z-1])) $size = $size + $this->child_property["size"][$z-1];
				}
				if ($size > 0) $ObjDIV->setProperty("size", $size);
				$istart = $istart + $colspan;
			}
			$pdf->headrow["row"] = 2;
		}
		//Header columns
		$ObjTR = $headObj->addChild($id."_caption", "tr", true);
		$i = 0;
		foreach ($this->child_property["objtype"] as $type)
		{
			$i++;
			$ObjTH = $ObjTR->addChild($id."_col$i", "th", true);
			$ObjTH->setProperty("nowrap","nowrap");
			if (isset($this->child_property["colwidth"][$i-1])) $ObjTH->setProperty("width", $this->child_property["colwidth"][$i-1]);
			$ObjDIV = $ObjTH->addChild($id."_".$i."_order", "div", true);
			if (isset($this->child_property["dsitem"][$i-1])) $label = $this->child_property["dsitem"][$i-1];
			if (isset($this->child_property["itemlabel"][$i-1])) $label = $this->child_property["itemlabel"][$i-1];
			$ObjDIV->setProperty("value", $label);
			if (isset($this->child_property["align"][$i-1])) $ObjDIV->setProperty("align", $this->child_property["align"][$i-1]);
			if (!empty($this->child_property["size"][$i-1])) $ObjDIV->setProperty("size", $this->child_property["size"][$i-1]);
			if (!empty($this->child_property["labelstyle"][$i-1])) $ObjDIV->setProperty("style", $this->child_property["labelstyle"][$i-1]);
		}

		$bodyObj = $tableOBJ->addChild($id."_tbody", "tbody", true);
		$n = 0;
		$dsObj->ds->dsMoveRow(0);
		$fetch = $dsObj->ds->property["fetch"];
		$dsObj->ds->property["fetch"] = "array";
		while($row = $dsObj->ds->dsGetRow())
		{
			$n++;
			$ObjTR = $bodyObj->addChild($id."_row$n","tr", true);
			$i = 0;
			foreach ($this->child_property["objtype"] as $type)
			{
				$i++;
				$ObjTD = $ObjTR->addChild($id."_row$n"."_col$i","td", true);
				$ObjItem = $ObjTD->addChild($id."_".$i."_1",$type, true);
				if (!empty($this->child_property["dsitem"][$i-1])) 
				{
					$col = $this->child_property["dsitem"][$i-1];
					$ObjItem->setProperty("value", array_key_exists($col, $row) ? $row[$col] : null);
				}
				foreach ($ObjItem->enumProperty(true) as $name)
				{
					if ($name=="dsobj" || $name=="dsitem") continue;
					if (isset($this->child_property[$name][$i-1]) && is_array($this->child_property[$name]))
						$ObjItem->setProperty($name, $this->child_property[$name][$i-1]);
				}
			}
		}
		$dsObj->ds->property["fetch"] = $fetch;
		$pdf->SetFont($this->property["pdffont"]["value"],'',$this->property["pdffontsize"]["value"]);
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
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$id = $this->property["id"]["value"];
		$width = $this->property["width"]["value"];
		$height = $this->property["height"]["value"];
		$class = $this->property["class"]["value"];	

		//Gridds
		$code = "\n".$tab."<div ".$this->getProperty("html", true, false)." style=\"width:$width; height:$height;\">";

		//Label
		if (!empty($this->property["label"]["value"])) $code .= "<div id=\"".$id."_label\" class=\"".$class."_label\" >".$this->property["label"]["value"]."</div>";

		//Header
		$code .= "\n\t".$tab."<div id=\"".$id."_heads\" class=\"".$class."_heads\" style=\"width:$width;\">";
		if (isset($this->child_property["headlabel"]))
		{
			$code .= "\n\t\t".$tab."<div id=\"".$id."_head\" style=\"position:relative;\">";
			foreach ($this->child_property["headlabel"] as $k => $text)
			{
				if (empty($this->child_property["headcol"][$k])) $this->child_property["headcol"][$k] = "1";
				$text = $this->parseValue($text);
				$code .= "\n\t\t\t".$tab."<div class=\"".$class."_head\" style=\"width:".$this->child_property["headcol"][$k]."px\">$text</div>";
			}
			$code .= "\n\t\t".$tab."</div>";
		}
	
		//Cols
		$code .= "\n\t\t".$tab."<div id=\"".$id."_col\" style=\"position:relative;\">";
		if (isset($this->child_property["objtype"]))
		{
			foreach ($this->child_property["objtype"] as $k => $type)
			{
				$text = (isset($this->child_property["itemlabel"][$k])) ? $this->child_property["itemlabel"][$k] : "";
				$itemobj = (isset($this->child_property["itemobj"][$k])) ? $this->child_property["itemobj"][$k] : "";
				$colalign = (isset($this->child_property["colalign"][$k])) ? "text-align:".$this->child_property["colalign"][$k]."; " : "";
				$colwidth = (isset($this->child_property["colwidth"][$k])) ? "width:".$this->child_property["colwidth"][$k].";" : "width:100px;";
				$order = (isset($this->child_property["order"][$k])) ? $this->child_property["order"][$k] : $this->property["order"]["value"];
				$order = ($order == "true") ? "DOWN" : "";
				$text = $this->parseValue($text);
				if ($itemobj == "checkbox")
				{
					$text = "<span id=\"".$id."_col_checkbox\" class=\"checkbox_default_undefined\"";
					if (isset($this->child_property["itemonclick"][$k])) $text .= " onclick=\"".$this->child_property["itemonclick"][$k]."\" ";
					$text .= "style=\"width: 28px;\">&nbsp;</span>";
					$this->setCodeJs('$("'.$id.'_col_checkbox").p = {classcheckbox:"checkbox_default",typeObj:"checkbox"};');
				} else {
					if (isset($this->child_property["itemonclick"][$k]))
					{
						$text = "<span onclick=\"".$this->child_property["itemonclick"][$k]."\" style=\"cursor: pointer;\">".$text."</span>";
					}
				}
				$code .= "\n\t\t\t".$tab."<div class=\"".$class."_col$order\" style=\"$colalign$colwidth\" onmousemove=\"GRIDDS.colMouseMove(this, event); \" onmousedown=\"GRIDDS.colMouseDown(this,	event);\">$text";
				$code .= "</div>";
			}
		}
		$code .= "\n\t\t".$tab."</div>";
		$code .= "\n\t".$tab."</div>";
		foreach ($this->child as $obj) $code .= "\t".$tab.$obj->codeHTML($tab."\t");
		$code .= "\n\t".$tab."<div id=\"".$id."_body\" class=\"".$class."_body\" style=\"width:$width;\">";
		$code .= "\n\t".$tab."</div>";
		$code .= "\n".$tab."</div>";
		return $code;
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		switch($name)
		{
			case "autoscroll":
				if ($this->property["autoscroll"]["value"] == "false")	$this->propertyJS["autoscroll"] = false;
			break;

			case "height":
				if ($this->property["height"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["height"]["value"] = "0px";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "GRIDDS.autoHeight('$id');");
				}
			break;
								
			case "width":
				if ($this->property["width"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["width"]["value"] = "0px";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "setTimeout('GRIDDS.autoWidth(\"$id\")', 100);");
				}
			break;
		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		if (empty($this->property["display"]["value"])) return "GRIDDS.refreshObj(\"".$this->property["id"]["value"]."\");";
		return "GRIDDS.displayObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>
