<?php
/**
* Object FIELDSET
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_xgridds extends ClsObject {

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
		$this->property["id"]				= array("value" => $id, "inherit" => false, "html" => true);
		$this->property["readonly"]		= array("value" => "true", "inherit" => false, "html" => false);
		$this->property["width"]			= array("value" => null, "inherit" => false, "html" => false);
		$this->property["height"]			= array("value" => null, "inherit" => false, "html" => false);
		$this->property["title"]			= array("value" => null, "inherit" => false, "html" => false);
		$this->property["background"]		= array("value" => "#CCCCCC", "inherit" => false, "html" => false);
		$this->property["scrollbar"]		= array("value" => "scroll", "inherit" => false, "html" => false);
		$this->property["pdffont"]			= array("value" => "Arial", "inherit" => false, "html" => true);
		$this->property["pdffontsize"]	= array("value" => "9", "inherit" => false, "html" => true);
		$this->property["dsobj"]			= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["java"]				= array("value" => "xgridds.js", "inherit" => false, "html" => false);
		$this->property["cssfile"]			= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsobj"]			= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["cellheight"]		= array("value" => "false", "inherit" => false, "html" => false);
		$this->multiObj = true;
	}

	/**
	* Set the properties of the object
	* @param string $name Name property
	* @param string $value 	Value of Property
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
 		global $xml;

		$id = $this->property["id"]["value"];
		$dsobj = $this->property["dsobj"]["value"];
		$class = $this->setCSS();	

		$this->addEventAfter($id, $dsobj."Refresh", "XGRIDDS.refreshObj(\"$id\");");
		$this->addEventAfter($id, $dsobj."Move", "XGRIDDS.moveRow(\"$id\");");
		$this->addEventBefore($id, $dsobj."SaveRow", "XGRIDDS.saveRow('$id');");

		//Body
		$divBody = $this->addChild($id."_body", "div", true);

		//Record
		$divRow = $divBody->addChild($id."_row0", "div" , true);
		$divRow->setProperty("style", "display:none;");
		$divRow->setProperty("onclick", "DS.moveRow('$dsobj', this.pos);");
		$divRow->setProperty("class",$class."_row");
		$i = 0;
		if (isset($this->child_property["objtype"]))
		{
		      foreach ($this->child_property["objtype"] as $type)
		      {
			      $i++;
			      $ObjItem = $divRow->addChild($id."_".$i."_0", $type);
			      if (isset($this->child_property["dsitem"][$i-1])) 
			      {
				      $dsitem = $this->child_property["dsitem"][$i-1];
				      $ObjItem->setProperty("dsobj", $dsobj);
				      $ObjItem->setProperty("dsitem", $dsitem);
				      $ObjItem->setProperty("value", "&nbsp;");
				      $ObjItem->setProperty("class", null);
			      }
			      foreach ($ObjItem->enumProperty(true) as $name)
			      {
				      if (isset($this->child_property[$name][$i-1]) && is_array($this->child_property[$name]))
					      $ObjItem->setProperty($name, $this->child_property[$name][$i-1]);
			      }
			      $ObjItem->BuildObj(); 
		      }
		}
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
 		$pdf->Ln();
 		global $xml;
 		$id = $this->property["id"]["value"];
 		$dsObj = $xml->getObjById($this->property["dsobj"]["value"]);

		//Body
		$divBody = $this->addChild($id."_body", "div", true);

 		$divBody->child = "";
 		$n = 0;
		$fetch = $dsObj->ds->property["fetch"];
		$dsObj->ds->property["fetch"] = "array";
 		while($row = $dsObj->ds->dsGetRow())
 		{
 			$n++;
 			$divRow = $divBody->addChild($id."_row$n","div");
 			$i = 0;
			foreach ($this->child_property["objtype"] as $type)
 			{
 				$i++;
				$ObjItem = $divRow->addChild($id."_".$i."_1", $type);	
				if (isset($this->child_property["dsitem"][$i-1])) 
				{
					$col = $this->child_property["dsitem"][$i-1];
					$ObjItem->setProperty("value", $row[$col]);
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
		$width = $this->property["width"]["value"];
		if ($this->property["width"]["value"]!="auto") $width = ($this->property["width"]["value"]+20)."px";
		$height = $this->property["height"]["value"];
		$code  = "\n".$tab."<div id=\"".$this->property["id"]["value"]."\" ";
		$code .= "style=\"padding:1px;overflow:".$this->property["scrollbar"]["value"].";background:".$this->property["background"]["value"].";width:$width;";
		$code .= "height:$height;\" class=\"".$this->property["class"]["value"]."\">";
		foreach ($this->child as $obj) $code .= "\t".$tab.$obj->codeHTML($tab);
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
			case "cellheight":
				$this->propertyJS[$name] = $this->property["cellheight"]["value"];
			break;
			case "height":
				if ($this->property["height"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["height"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
				}
			break;

			case "scrollbar"://true,false,auto
					$scroll = $this->property["scrollbar"]["value"];
					if ($scroll=="true") $scroll = "scroll";
					if ($scroll=="false") $scroll = "none";
					$this->property["scrollbar"]["value"] = $scroll;
			break;

			case "width":
				if ($this->property["width"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["width"]["value"] == "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
				}
			break;
		}

	}

	/**
	* Generate the code XML
	* @param string $tab Tabs
	*/
	public function codeXML($tab = "") 
	{
		$code ="$tab<".$this->getPropertyName("id")." typeobj=\"".get_class($this)."\" ".$this->getProperty("xml", true, false).">\n";
		for($i = 0; $i < count($this->child_property["objtype"]); $i++){
			$label = "";
			if (isset($this->child_property["label"][$i])) $label = " label = \"".$this->child_property["label"][$i]."\"";
			$code .= "$tab\t<col".($i+1)."$label objtype = \"".$this->child_property["objtype"][$i]."\" dsitemorder = \"".$this->child_property["dsitemorder"][$i]."\" dsitem = \"".$this->child_property["dsitem"][$i]."\" />\n";
		}		
		return $code;	
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "XGRIDDS.refreshObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>