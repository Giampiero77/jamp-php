<?php
/**
* Object DSNAV
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_dsnav extends ClsObject {

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
		$this->property["id"] 	 		 = array("value" => $id, "inherit" => false, "html" => true);
		$this->property["class"] 		 = array("value" => "dsnav", "inherit" => false, "html" => true);
		$this->property["btSave"]		 = array("value" => "true", "inherit" => false, "html" => false);
		$this->property["btNew"] 		 = array("value" => "true", "inherit" => false, "html" => false);
		$this->property["btDelete"]		 = array("value" => "true", "inherit" => false, "html" => false);
		$this->property["btNav"] 		 = array("value" => "true", "inherit" => false, "html" => false);
		$this->property["btTotal"]  	 = array("value" => "true", "inherit" => false, "html" => false);
		$this->property["btCancel"] 	 = array("value" => "true", "inherit" => false, "html" => false);
		$this->property["btPage"] 		 = array("value" => "true", "inherit" => false, "html" => false);
		$this->property["btReload"] 	 = array("value" => "true", "inherit" => false, "html" => false);
		$this->property["btPrint"]  	 = array("value" => "true", "inherit" => false, "html" => false);
		$this->property["btXls"]	  	 = array("value" => "false", "inherit" => false, "html" => false);
		$this->property["objprint"] 	 = array("value" => null, "inherit" => false, "html" => false);
		$this->property["fnzprint"] 	 = array("value" => "window.print('pdf');", "inherit" => false, "html" => false);
		$this->property["pageprint"] 	 = array("value" => null, "inherit" => false, "html" => false);
		$this->property["fnzxls"] 	 	 = array("value" => "window.print('xls');", "inherit" => false, "html" => false);
		$this->property["label"] 		 = array("value" => "", "inherit" => false, "html" => false);
		$this->property["maxlength"]	 = array("value" => 255, "inherit" => false, "html" => false);
		$this->property["size"] 		 = array("value" => 30, "inherit" => false, "html" => false);
		$this->property["dssearch"] 	 = array("value" => null, "inherit" => false, "html" => false);
		$this->property["dssearchrange"] = array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsautosearch"]  = array("value" => "false", "inherit" => false, "html" => false);
		$this->property["dsfullsearch"]	 = array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsobj"] 	 	 = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["java"]  	 	 = array("value" => "dsnav.js", "inherit" => false, "html" => false);
 		$this->property["cssfile"]   	 = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsobj"]  		 = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]  		 = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["objtype"]  	 = array("value" => null, "inherit" => false, "html" => false);
		$this->property["searchonkeyup"] = array("value" => "false", "inherit" => false, "html" => false);
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
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$id = $this->property["id"]["value"];
		$class = $this->property["class"]["value"];
		$code = "\n$tab<div ".$this->getProperty("html", true, false).">";
		$obj = $this->property["dsobj"]["value"];
		if ($this->property["btNew"]["value"] == "true") 
		{
			$code .= "\n$tab\t<div accessKey=\"n\" class=\"".$class."_new\" id=\"".$id."_new\" title=\"".LANG::translate("DSNAV001")." CTRL+ALT+N\" onclick=\"if (this.className == '".$class."_new') DS.dsnew('$obj');\">&nbsp;</div>";
		}
		if ($this->property["btSave"]["value"] == "true") 
		{
			$code .= "\n$tab\t<div accessKey=\"s\" class=\"".$class."_save\" id=\"".$id."_save\" title=\"".LANG::translate("DSNAV002")." CTRL+ALT+S\" onclick=\"if (this.className == '".$class."_save') DS.dssave('$obj');\">&nbsp;</div>";
		}	
		if ($this->property["btDelete"]["value"] == "true") 
		{
			$code .= "\n$tab\t<div accessKey=\"d\" class=\"".$class."_delete\" id=\"".$id."_delete\" title=\"".LANG::translate("DSNAV003")." CTRL+ALT+D\" onclick=\"if (this.className == '".$class."_delete') DSNAV.dsdelete('$obj', event);\">&nbsp;</div>";
		}
		if (!is_null($this->property["dsfullsearch"]["value"])) 
		{
			$code .= "\n$tab\t<div class=\"".$class."_fullsearch\" id=\"".$id."_fullsearch\" title=\"".LANG::translate("DSNAV010")."\" onclick=\"if (this.className == '".$class."_fullsearch') DSNAV.fullsearch('$id','$obj');\">&nbsp;</div>";
		}
		if ($this->property["btCancel"]["value"] == "true") 
		{
			$code .= "\n$tab\t<div accessKey=\"c\" class=\"".$class."_cancel\" id=\"".$id."_cancel\" title=\"".LANG::translate("DSNAV004")." CTRL+ALT+C\" onclick=\"if (this.className == '".$class."_cancel') DSNAV.cancel('$id','$obj');\">&nbsp;</div>";
		}
		if ($this->property["btNav"]["value"] == "true") 
		{
			$code .= "\n$tab\t<div class=\"".$class."_first\" id=\"".$id."_first\" title=\"".LANG::translate("DSNAV005")."\" onclick=\"if (this.className == '".$class."_first') ".$obj."MoveFirst();\">&nbsp;</div>";
			$code .= "\n$tab\t<div class=\"".$class."_prev\" id=\"".$id."_prev\" title=\"".LANG::translate("DSNAV006")."\" onclick=\"if (this.className == '".$class."_prev') ".$obj."MovePrev();\">&nbsp;</div>";
		}
		if ($this->property["btTotal"]["value"] == "true") $code .= "\n$tab\t<div class=\"".$class."_total\" id=\"".$id."_total\">0 / 0</div>";
		if ($this->property["btNav"]["value"] == "true") 
		{
			$code .= "\n$tab\t<div class=\"".$class."_next\" id=\"".$id."_next\" title=\"".LANG::translate("DSNAV007")."\" onclick=\"if (this.className == '".$class."_next') ".$obj."MoveNext();\">&nbsp;</div>";
			$code .= "\n$tab\t<div class=\"".$class."_last\" id=\"".$id."_last\" title=\"".LANG::translate("DSNAV008")."\" onclick=\"if (this.className == '".$class."_last') ".$obj."MoveLast();\">&nbsp;</div>";
		}
		if ($this->property["btReload"]["value"] == "true") $code .= "\n$tab\t<div accessKey=\"r\" class=\"".$class."_reload\" id=\"".$id."_reload\" title=\"".LANG::translate("DSNAV011")."CTRL+ALT+R\" onclick=\"DS.reload('$obj');\">&nbsp;</div>";
		if ($this->property["btPrint"]["value"] == "true")
		{
			$fnz = $this->property["fnzprint"]["value"];
			if ($fnz == null) $fnz = "SYSTEMBROWSER.printContent('".$this->property["objprint"]["value"]."');";
			$code .= "\n$tab\t<div class=\"".$class."_print\" id=\"".$id."_print\" title=\"".LANG::translate("DSNAV012")."\" onclick=\"$fnz\">&nbsp;</div>";
		}
		if ($this->property["btXls"]["value"] == "true")
		{
			$fnz = $this->property["fnzxls"]["value"];
			$code .= "\n$tab\t<div class=\"".$class."_xls\" id=\"".$id."_xls\" title=\"".LANG::translate("DSNAV013")."\" onclick=\"$fnz\">&nbsp;</div>";
		}
		if ($this->property["btPage"]["value"] == "true") $code .= "\n$tab\t<div class=\"".$class."_totalpage\"><div style=\"float:left\">".LANG::translate("DSNAV009")."</div><select class=\"".$class."_totalpage\" id=\"".$id."_page\" onchange=\"DSNAV.page('$obj', this);\"><option></option></select></div><!--[if IE]><style type=\"text/css\"> select.".$class."_totalpage { font-size: 8px; padding:0px; } </style> <![endif]--> ";

		if (!is_null($this->property["dssearch"]["value"])) 
		{
			if ($this->property["dsautosearch"]["value"]=="true")
			{
	 			$code .= "\n$tab<div class=\"".$class."_find\"><LABEL style=\"margin-top:3px;\">".$this->property["label"]["value"]."</label>";
				$maxlength = $this->property["maxlength"]["value"];
				$size = $this->property["size"]["value"];
				$this->addEventListener("window", "load", "pageLoad");
				$this->addEvent("page", "pageLoad", "$('".$id."_autosearch').selectedIndex=-1;");
				$code .= "<select id=\"".$id."_autosearch\" class=\"".$class."_find\" onchange=\"DSNAV.searchAuto(this,'".$id."',".$this->property["searchonkeyup"]["value"].");\"><option selected></option>";	
				global $xml;
				$ds = $xml->getObjById($obj, "ds");
				$ds->ds->dsConnect();
				$ds->ds->dsDescribe($ds->ds->property["dstable"]);
				$field = array();
				if (empty($ds->ds->property["join"]))
				{
					while($row = $ds->ds->dsGetRow()) if(!empty($row->Comment)) $field["$row->Field|$row->Type"] = $row->Comment;
				}
				else 
				{
					while($row = $ds->ds->dsGetRow()) if(!empty($row->Comment)) $field[$ds->ds->property["dstable"].".$row->Field|$row->Type"] = $row->Comment;
					$ds->ds->dsDescribe($ds->ds->property["join"]);
					while($row = $ds->ds->dsGetRow()) if(!empty($row->Comment)) $field[$ds->ds->property["dstable"].".$row->Field|$row->Type"] = $row->Comment;
				}
				asort($field);
				$jscode = "";
				foreach ($field as $value => $comment)
				{
					$comment = explode("@",$comment);
					if (isset($comment[1])) $value .= "@".$comment[1];
					$code .= "<option value=\"$value\">$comment[0]</option>";
				}
				
				$code .= "</select><div id=\"".$id."_field\" style=\"display:inline\"></div></div>";
				$code .= "\n$tab\t<div class=\"".$class."_findicon\" id=\"".$id."_find\" title=\"".LANG::translate("DSNAV010")."\" onclick=\"DSNAV.dsfindAuto('$id');\">&nbsp;</div>";
				$code .="<!--[if IE]><style type=\"text/css\">input.".$class."_find { padding:0px } </style> <![endif]--> ";				
			} else {
	 			$code .= "\n$tab<div class=\"".$class."_find\"><LABEL style=\"margin-top:3px;\">".$this->property["label"]["value"]."</label>";
				$maxlength = $this->property["maxlength"]["value"];
				$size = $this->property["size"]["value"];
				if ($this->property["searchonkeyup"]["value"] == "false")
				{
					$code .= "\n$tab<input class=\"".$class."_find\" type=\"text\" id=\"".$id."_search\" onkeyup=\"DSNAV.dskeyfind('$id', event)\" size=\"$size\" maxlength=\"$maxlength\"></div>";
					$code .= "\n$tab\t<div class=\"".$class."_findicon\" id=\"".$id."_find\" title=\"".LANG::translate("DSNAV010")."\" onclick=\"DSNAV.dsfind('$id');\">&nbsp;</div>";
				}
				else $code .= "\n$tab<input class=\"".$class."_find\" type=\"text\" id=\"".$id."_search\" onkeyup=\"DSNAV.dsfind('$id');\" size=\"$size\" maxlength=\"$maxlength\"></div>";
				$code .="<!--[if IE]><style type=\"text/css\">input.".$class."_find { padding:0px } </style> <![endif]--> ";
			}
		}

		if (isset($this->child_property["image"]))
		{
			for($i = 0; $i < count($this->child_property["image"]); $i++)
			{
				$idchild = "";
				if (isset($this->child_property["id"][$i])) $idchild = " id=\"".$this->parseValue($this->child_property["id"][$i])."\"";
				$code .= "\n$tab\t<div$idchild class=\"".$class."_bt_custom\" onclick=\"".$this->parseValue($this->child_property["onclick"][$i])."\"";
				if (isset($this->child_property["image"][$i])) $code .= " style=\"background-image : url('".$this->parseValue($this->child_property["image"][$i])."');  background-repeat:no-repeat; background-position:center;\"";
				if ($this->child_property["title"][$i]) $code .= " title=\"".$this->parseValue($this->child_property["title"][$i])."\" ";
				$code .= ">&nbsp;</div>";
			}
		}
		$code .= "\n$tab</div>";
		return $code;
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$this->setCSS();
		$id = $this->property["id"]["value"];
		$dsobj = $this->property["dsobj"]["value"];
		$this->propertyJS["DSsearch"] = $this->property["dssearch"]["value"];
		if (!empty($this->property["pageprint"]["value"]))
		{
			$this->property["fnzprint"]["value"] = "DSNAV.print('$id', '".$this->property["pageprint"]["value"]."', false);";
			$this->property["fnzxls"]["value"] = "DSNAV.print('$id', '".$this->property["pageprint"]["value"]."', true);";
		}
		if (!empty($this->property["dssearchrange"]["value"]))
		{
			$this->propertyJS["DSsearchRange"] = $this->property["dssearchrange"]["value"];
			$this->property["java"]["value"] = array($this->property["java"]["value"],"calendar.js");
			$template = empty($this->property["template"]["value"]) ? "default" : $this->property["template"]["value"];
			$this->property["cssfile"]["value"] = array($this->property["cssfile"]["value"], "objcss/$template/calendar.css");
		}
		$this->propertyJS["DSfullsearch"] = $this->property["dsfullsearch"]["value"];
		$this->addEvent($id, $dsobj."Move", "DSNAV.refreshObj(\"$id\");");
		$this->addEvent($id, $dsobj."Refresh", "DSNAV.refreshObj(\"$id\");");
		$this->addEventAfter($id, $dsobj."ChangeItem", "DSNAV.ChangeItem(\"$id\");");
		global $DS_VALIDATE_RULE;
		if (isset($DS_VALIDATE_RULE[$dsobj])) $this->propertyJS["DSvalidate"] = true;
		if (empty($this->property["dsobj"]["value"]))
		{
			$this->property["btSave"]["value"] = "false";
			$this->property["btNew"]["value"] = "false";
			$this->property["btDelete"]["value"] = "false";
			$this->property["btNav"]["value"] = "false";
			$this->property["btTotal"]["value"] = "false";
			$this->property["btCancel"]["value"] = "false";
			$this->property["btPage"]["value"] = "false";
			$this->property["btReload"]["value"] = "false";
			$this->property["btPrint"]["value"] = "false";
		}
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
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "DSNAV.refreshObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>
