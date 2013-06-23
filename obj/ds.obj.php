<?php
/**
* Object DS
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_ds extends ClsObject {
	/**
	* @var $ds Contains the class for managing the DS
	*/
	var $ds = null;
	var $locktime = null;

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property							= array();
		$this->property["id"]					= array("value" => $id,  	"inherit" => false, "html" => true);
		$this->property["debug"]				= array("value" => "false",  "inherit" => true, "html" => false);
		$this->property["out"]					= array("value" => "json", 	"inherit" => false, "html" => false); //json,xml
		$this->property["focusnew"]				= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["focustabnew"]			= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["dshost"]				= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["dsport"]				= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["dsuser"]				= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["dspwd"]				= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["dsfaillogin"]			= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["faillogin"]			= array("value" => "0", 	"inherit" => false, "html" => false);
		$this->property["conn"]					= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["store"]				= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["event"]				= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["xml"]					= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["encpwd"]				= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["printxml"]				= array("value" => "true", 	"inherit" => false, "html" => false);
		$this->property["loadall"]				= array("value" => "true", 	"inherit" => false, "html" => false);
		$this->property["load"]					= array("value" => null, 	"inherit" => false, "html" => false);
		$this->property["readonly"]				= array("value" => null,	"inherit" => false, "html" => false);
		$this->property["confirm"]				= array("value" => "false",	"inherit" => false, "html" => false);
 		$this->property["objtype"]  			= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["unsetpost"]  			= array("value" => null, "inherit" => false, "html" => false);
 			
		$this->property["selecteditems"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsdefault"]			= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dstable"]				= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsorder"]				= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dswhere"]				= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dskey"]				= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dssavetype"]			= array("value" => "row", "inherit" => false, "html" => false); //row, table

		$this->property["join"]					= array("value" => null, "inherit" => false, "html" => false);
		$this->property["joinsave"]				= array("value" => "join",	"inherit" => false, "html" => false);
		$this->property["jointype"]				= array("value" => null, "inherit" => false, "html" => false);
		$this->property["joinrule"]				= array("value" => null, "inherit" => false, "html" => false);

		$this->property["dslock"]				= array("value" => null,    "inherit" => false, "html" => false);
		$this->property["dsrefresh"]			= array("value" => null,    "inherit" => false, "html" => false);
		$this->property["dsreferences"]			= array("value" => null,    "inherit" => false, "html" => false);
		$this->property["referencestable"]		= array("value" => null,   "inherit" => false, "html" => false);
		$this->property["referenceskey"]		= array("value" => null,   "inherit" => false, "html" => false);
		$this->property["foreignkey"]			= array("value" => null,   "inherit" => false, "html" => false);
		$this->property["deleteoncascate"]		= array("value" => "false", "inherit" => false, "html" => false);
		$this->property["deleteall"]			= array("value" => "false", "inherit" => false, "html" => false);

		$this->property["dsengine"]				= array("value" => null,"inherit" => false, "html" => false);
		$this->property["dsquery_select"]		= array("value" => null,"inherit" => false, "html" => false);
		$this->property["dsquery_insert"]		= array("value" => null,"inherit" => false, "html" => false);
		$this->property["dsquery_update"]		= array("value" => null,"inherit" => false, "html" => false);
		$this->property["dsquery_delete"]		= array("value" => null,"inherit" => false, "html" => false);
		$this->property["dsquery_deleteall"]	= array("value" => null,"inherit" => false, "html" => false);
		$this->property["dsextraquery"]			= array("value" => null,"inherit" => false, "html" => false);
		$this->property["dslimit"]				= array("value" => null,"inherit" => false, "html" => false);
		$this->property["action"]				= array("value" => null,"inherit" => false, "html" => false);
		$this->property["fetch"]				= array("value" => "object", "inherit" => false, "html" => false);
		$this->property["ghostdata"]			= array("value" => null, "inherit" => false, "html" => false);

		/**************************** SPECIFICATIONS FOR POSTGRESQL DATABASE *******************************/
		$this->property["sslmode"]	= array("value" => null, "inherit" => false, "html" => false);
		/***************************************************************************************************/

		/********************** SPECIFICATIONS FOR PROPERTY HIERARCHICAL STRUCTURES ************************/
		$this->property["dsparentkey"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsname"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["alias"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["base"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["select"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["filter"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["justthese"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["scope"]	= array("value" => null, "inherit" => false, "html" => false);
		/***************************************************************************************************/

		/************************ SPECIFICATIONS FOR PROPERTY RECORD STRUCTURES *****************************/
		$this->property["recname"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["reclength"]	= array("value" => null, "inherit" => false, "html" => false);
		/***************************************************************************************************/

		/********************************** PROPERTY FOR SSH CONNECTION ************************************/
		$this->property["privkeyfile"] = array("value" => null,	"inherit" => false, "html" => false);
		$this->property["pubkeyfile"]  = array("value" => null,	"inherit" => false, "html" => false);
		/***************************************************************************************************/

		/********************************** PROPERTY FOR CSV CONNECTION ************************************/
		$this->property["fieldname"] 	  = array("value" => null, "inherit" => false, "html" => false);
		$this->property["fieldseparator"] = array("value" => null, "inherit" => false, "html" => false);
		$this->property["fieldencloser"]   = array("value" => null, "inherit" => false, "html" => false);
		$this->property["fieldescape"]   = array("value" => null, "inherit" => false, "html" => false);
		/***************************************************************************************************/

		/********************************** PROPERTY FOR SMS CONNECTION ************************************/
		$this->property["prefix"] 	  = array("value" => null, "inherit" => false, "html" => false);
		$this->property["verbose"]   = array("value" => "1", "inherit" => false, "html" => false);
		$this->property["gateway"]   = array("value" => null, "inherit" => false, "html" => false);
		$this->property["carriergateways"] = array("value" => null, "inherit" => false, "html" => false);
		$this->property["answerrecipients"] = array("value" => null, "inherit" => false, "html" => false);
		/***************************************************************************************************/

		$this->property["format"]  	 = array("value" => null,	"inherit" => false, "html" => false);
		// false NOT ATTACH THE EVENT TO SAVE THE DATASOURCE
		$this->property["unload"]  	 = array("value" => null,	"inherit" => false, "html" => false);

 		$this->property["java"] = array("value" => "ds.js", "inherit" => false, "html" => false);

		$this->addEventBefore($id, $id."BeforeSave", "");
		$this->addEventBefore($id, $id."SaveRow", "");
		$this->addEventBefore($id, $id."ChangeItem", "");

		$this->addEventBefore($id, $id."MoveNext", "DS.moveNext(\"$id\");");
		$this->addEventBefore($id, $id."MoveNext", $id."Move();");

		$this->addEventBefore($id, $id."MovePrev", "DS.movePrev(\"$id\");");
		$this->addEventBefore($id, $id."MovePrev", $id."Move();");

		$this->addEventBefore($id, $id."MoveLast", "DS.moveLast(\"$id\");");
		$this->addEventBefore($id, $id."MoveLast", $id."Move();");

		$this->addEventBefore($id, $id."MoveFirst", "DS.moveFirst(\"$id\");");
		$this->addEventBefore($id, $id."MoveFirst", $id."Move();");

		$this->addEventBefore($id, "dsRefresh", "DS.refreshObj(\"$id\");");
		$this->addEventBefore($id, $id."Move", "//$id");

		$this->property["action"]["value"] = $_SERVER['PHP_SELF'];

		if (!isset($_POST["data"])) $this->dsRules($id);
	}

	/**
	* Manually connect the ds and executes the query selection
	*/
	public function manualConnect()
	{	
		$this->ds->dsConnect();
		if (isset($_REQUEST["dsorder"])) $this->setProperty("dsorder", $_REQUEST["dsorder"]);
		$idorder=$this->property["id"]["value"]."order";
		if (isset($_REQUEST[$idorder])) $this->setProperty("dsorder", $_REQUEST[$idorder]);
		if (isset($_REQUEST["dswhere"]))
		{
			$text = stripslashes($_REQUEST["dswhere"]);
			$this->ds->dsQueryFilter($text);
		}
		$idwhere=$this->property["id"]["value"]."where";
		if (isset($_REQUEST[$idwhere]))
		{
			$text = stripslashes($_REQUEST[$idwhere]);
			$this->ds->dsQueryFilter($text);
		}
		$event = "data_select_before";
		$this->callEvent($event, $this);
		$this->ds->dsQuerySelect($this->property["dsquery_select"]["value"]);	
		$event = "data_select_after";
		$this->callEvent($event, $this);
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
	* Call the event
	*/
	function callEvent($event, $param=null)
	{
		return userEvent::call($event, $param);
	}

	/**	
	* Returns the data of DS in XML format
	*/
	public function codeDsXML($filterds = null)
	{
		$code = "";
		if ($this->property["printxml"]["value"] != "true") return;
		if (($this->property["load"]["value"] == "false" || $this->property["loadall"]["value"] == "false") && $_REQUEST["data"] == "loadall")
		{
		  $code .= "$('".$this->property["id"]["value"]."').DSresult = new Array();";
		  $code .= "AJAX.setDsJSON('".$this->property["id"]["value"]."',0,0,0,0);";
		  return $this->_complete($code);
		}
		if ($this->property["load"]["value"] == "false") return $code;
		if (!empty($this->property["xml"]["value"])) return $this->property["xml"]["value"];
		if (is_null($this->property["conn"]["value"])) return;
		$this->ds->dsConnect();

		if (isset($_REQUEST["dsorder"])) $this->setProperty("dsorder", $_REQUEST["dsorder"]);
		$idorder=$this->property["id"]["value"]."order";
		if (isset($_REQUEST[$idorder])) $this->setProperty("dsorder", $_REQUEST[$idorder]);
		if (isset($_REQUEST["dswhere"]))
		{
			if (is_array($_REQUEST["dswhere"])) $text = stripslashes($_POST["dswhere"]);
			else $text = stripslashes($_REQUEST["dswhere"]);
			$this->ds->dsQueryFilter($text);
		}
		$idwhere=$this->property["id"]["value"]."where";
		if (isset($_REQUEST[$idwhere]))
		{
			if (is_array($_REQUEST[$idwhere])) $text = stripslashes($_POST[$idwhere]);
			else $text = stripslashes($_REQUEST[$idwhere]);
			$this->ds->dsQueryFilter($text);
		}
		if (isset($_REQUEST['start']))
		{
			if (is_array($_REQUEST['start']))  $this->ds->property["start"] = intval($_POST['start']);
			else $this->ds->property["start"] = intval($_REQUEST['start']);
		}
		$return = $this->callEvent("data_select_before", $this);
		if(is_null($return) || ($return == true)) $this->ds->dsQuerySelect($this->property["dsquery_select"]["value"]);
		$return = $this->callEvent("data_select_after", $this);
		if(is_null($return) || ($return == true))
		{
			if ($this->property["dssavetype"]["value"] == "table") $this->ds->property["inslast"] = -1;
			//Insert/Update
			if ($this->ds->property["inslast"] > -1)
			{
				$id = $this->property["id"]["value"];
				if ($this->ds->property["inslast"] == 0) $code .= $this->_complete("\nDS.dsUpdate(\"$id\", \"$this->locktime\");"); //Update
				else $code .= $this->_complete("\nDS.dsInsert(\"$id\", \"".$this->ds->property["inslast"]."\");"); // Insert
			}
			else //Return XML
			{
				$id = $this->property["id"]["value"];
				if (!isset($this->ds->property['tot'])) $this->ds->property['tot'] = $this->ds->dsCountRow();
 				if (!empty($this->property["xml"]["value"])) 
				{
					if ($this->property["out"]["value"] == "json")
					{
						$tmp = "\n$('$id').DSresult = [";
						$tmp .= $this->property["xml"]["value"];
						$tmp .= "];";
						$tmp .= "\nAJAX.setDsJSON('".$id."',".$this->ds->property['start'].",".$this->ds->property['end'].",".$this->ds->property['tot'].",".$this->ds->property['limit'].");";
						$code .= $this->_complete($tmp);
					}
					else
					{
						$code .= "<$id start=\"".$this->ds->property['start']."\" end=\"".$this->ds->property['end']."\" limit=\"".$this->ds->property['limit']."\" tot=\"".$this->ds->property['tot']."\" action=\"".$this->property["action"]["value"]."\" order=\"".$this->property["dsorder"]["value"]."\">\n";
						$code .= $this->property["xml"]["value"]."</$id>\n";
					}
				}
				else if ($this->property["out"]["value"] == "json") //OUT JSON
				{
 					global $system;
					require_once($system->dir_real_jamp."/".$system->dir_class."/json.php");
					$json = new Services_JSON();
					$tmp = "\n$('$id').DSresult = [";
					$i = 1;
					$pre = ($this->property["fetch"]["value"] == "row") ? "col_" : "";
					while($row = $this->ds->dsGetRow())
					{
						$arrRow = array();
						foreach ($row as $k => $item) 
						{
							$tag = $pre.$k;
							if (!preg_match("/^[A-Za-z0-9_\-àéèìùò]+$/", $tag)) ClsError::showError("OBJ009", "", $tag);
							$arrRow[] = $json->encode($tag).":".$json->encode($item);
						}
						$tmp .= "\n{".implode(",", $arrRow)."},";
						$i++;
					}
					if ($i>1) $tmp = substr($tmp, 0, -1);
					$tmp .= "];";
					$tmp .= "\nAJAX.setDsJSON('".$id."',".$this->ds->property['start'].",".$this->ds->property['end'].",".$this->ds->property['tot'].",".$this->ds->property['limit'].");";
					$code .= $this->_complete($tmp);
				}
				else //OUT XML
				{
					if (!isset($this->ds->property['tot'])) $this->ds->property['tot'] = $this->ds->dsCountRow();
					$code .= "<$id start=\"".$this->ds->property['start']."\" end=\"".$this->ds->property['end']."\" limit=\"".$this->ds->property['limit']."\" tot=\"".$this->ds->property['tot']."\" action=\"".$this->property["action"]["value"]."\" order=\"".$this->property["dsorder"]["value"]."\">\n";
					while($row = $this->ds->dsGetRow())
					{
						$code .= "<row>\n";
						$pre = ($this->property["fetch"]["value"] == "row") ? "col_" : "";
						foreach ($row as $k => $item) 
						{
							$tag = $pre.$k;
							if (!preg_match("/^[A-Za-z0-9_\-àéèìùò]+$/", $tag)) ClsError::showError("OBJ009", "", $tag);
							$code .= "<$tag>".htmlspecialchars($item)."</$tag>\n";
						}
						$code .= "</row>\n";
					}
					$code .= "</$id>\n";
				}
			}
		}
		return $code;
	}

	/**
	* 	Create rules for validation
	*/
	public function dsRules($id)
	{
		global $DS_VALIDATE_RULE;
		if (isset($DS_VALIDATE_RULE[$id]))
		{
			$this->setCodeJsValidate("function Validate$id()");
			$this->setCodeJsValidate("{");
			$this->setCodeJsValidate($DS_VALIDATE_RULE[$id]);
			$this->setCodeJsValidate("}");
			$this->setCodeJsValidate("$(\"$id\").DSvalidate = true;");
		}
	}

	/**
	* Executes the query update
	* @param array Post
	*/
	public function dsUpdate($post)
	{
		if ($this->property["readonly"]["value"]=="true") return;
		foreach ($post as $name => $ps)
		{
		  if (preg_match_all('/\$\$[\w-_]{1,}\$\$/',$name, $vars)) unset($post[$name]);
		}
		$pre = $this->ds->property["where"];
		$this->ds->property["where"] = array();
		$keyname = $post["keyname"];
		$key = $post["keynamevalue"];
		unset($post["data"]);
		unset($post["dsobjname"]);
		unset($post["keyname"]);
		unset($post["keynamevalue"]);
		unset($post["start"]);
		$post = $this->unsetPost($post);
		$this->ds->dsQueryFilter(null, $keyname, $key);
		if(!empty($this->property["dslock"]["value"]))
		{
			if ($post[$this->property["dslock"]["value"]]!="force") $this->ds->dsQueryFilter(null, $this->property["dslock"]["value"], $post[$this->property["dslock"]["value"]]);
			$this->locktime = time();
			$post[$this->property["dslock"]["value"]] = $this->locktime;
		}
		$this->ds->property["item"] = $post;
		$this->ds->dsConnect();
		$tot = $this->ds->dsQueryUpdate($this->property["dsquery_update"]["value"]);
		if(!empty($this->property["dslock"]["value"]))
		{
			global $event;
			if ($tot==0)
			{
				$code ="
					DS.dschange($('".$this->property["id"]["value"]."'));
					if(confirm('".LANG::translate("DS010")."'))
					{
						var dsobj=$('".$this->property["id"]["value"]."');
						dsobj.DSresult[dsobj.DSpos][dsobj.p.dslock]='force';
						DS.dssave('".$this->property["id"]["value"]."');
					} else SYSTEMEVENT.showErrorGhost('".LANG::translate("DS011")."','".LANG::translate("DS012")."');
				";
				$event->returnRequest(null,$code);
				die();
			}
		} 
		$this->ds->property["where"] = $pre;
	}

	/**
	* Executes the query authentication
	* @param array Post
	*/
	public function dsLogin($post)
	{
		$this->ds->login($post);
	}

	/**
	* Execute the query for the change password
	* @param array Post
	*/
	public function dsChangePasswd($post)
	{
		if ($this->property["readonly"]["value"]=="true") return;
		$pwd[$post["itempwd"]] = $post["pwd"];
		$this->ds->property["item"] = $pwd;
		return $this->ds->changepasswd($post);
	}

	/**
	* Executes the query insert
	* @param array Post
	*/
	 public function dsNew($post, $update = false)
	 {
		if ($this->property["readonly"]["value"]=="true") return;
		foreach ($post as $name => $ps)
		{
		  if (preg_match_all('/\$\$[\w-_]{1,}\$\$/',$name, $vars)) unset($post[$name]);
		}
		$pre = $this->ds->property["where"];
		$this->ds->property["where"] = array();
		unset($post["data"]);
		unset($post["dsobjname"]);
		$keyname = isset($post["keyname"]) ? $post["keyname"]: "";
		$key = isset($post["keynamevalue"]) ? $post["keynamevalue"]: "";
		unset($post["keynamevalue"]);
		unset($post["keyname"]);
		unset($post["start"]);
		$post = $this->unsetPost($post);
		$this->ds->property["item"] = $post;
		$this->ds->dsConnect();
		if ($update) 
		{
			 $this->ds->dsQueryFilter(null, $keyname, $key);
			 $this->ds->dsQuerySelect($this->property["dsquery_select"]["value"]);
			 if ($this->ds->dsCountRow()==0) $this->ds->dsQueryInsert($this->property["dsquery_insert"]["value"]); 
			 else $this->ds->dsQueryUpdate($this->property["dsquery_update"]["value"]);
		}
		else $this->ds->dsQueryInsert($this->property["dsquery_insert"]["value"]);
		$this->ds->property["where"] = $pre;
	}

	/**
	* Delete the record
	* @param array Post
	*/
	public function dsDelete($post)
	{
		if ($this->property["readonly"]["value"]=="true") return;
		$pre = $this->ds->property["where"];
		$this->ds->dsConnect();
		if (empty($this->property["dsquery_delete"]["value"]))
		{
			$keyname = $post["keyname"];
			$key = $post["keynamevalue"];
			$this->ds->dsQueryFilter(null, $keyname, $key);
			$this->ds->dsQueryDelete();
		} 
		else $this->ds->dsQueryDelete($this->property["dsquery_delete"]["value"]);
		$this->ds->property["where"] = $pre;
	}

	/**
	* Delete all record
	* @param array Post
	*/
	public function dsDeleteAll()
	{
		if ($this->property["deleteall"]["value"] == "false") return;
		if ($this->property["readonly"]["value"] == "true") return;
		$this->ds->dsConnect();
		$this->ds->dsQueryDeleteAll($this->property["dsquery_delete"]["value"]);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		return "\n$tab<div ".$this->getProperty("html", true, false)."></div>";
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$id1 = $this->property["id"]["value"];
		if ($this->property["event"]["value"]!="none" && $this->property["unload"]["value"]!="false")
		{
			$this->addEventListener("window", "unload", "pageUnload");
			$this->addEvent("page", "pageUnload", "DS.dssave(\"$id1\", ($(\"$id1\").p.DSconfirm != false));");
		}
		if (!empty($this->property["dsrefresh"]["value"]))
		{
			foreach (explode(",", $this->property["dsrefresh"]["value"]) as $id2)
			{
				$this->addEventAfter($id1, $id1."Refresh", "AJAX.refreshdslink('$id2');");
				$this->propertyJS["DSrefresh"] = $this->property["dsrefresh"]["value"];
			}
		}
		if (!empty($this->property["dsreferences"]["value"])) 
		{
			$idrefs = explode(",",$this->property["dsreferences"]["value"]);
			foreach($idrefs as $idref) $this->addEventAfter($idref, $idref."Move", "AJAX.dslink('$id1');");
		}
		$this->propertyJS["DSaction"] = $this->property["action"]["value"];
		if (empty($this->property["dskey"]["value"]) && !empty($this->ds->property["dskey"])) $this->propertyJS["DSkey"] = $this->ds->property["dskey"];

		if (empty($this->property["dsparentkey"]["value"]) && !empty($this->ds->property["dsparentkey"])) 
			$this->propertyJS["DSparentkey"] = $this->ds->property["dsparentkey"];

		if (empty($this->property["dsname"]["value"]) && !empty($this->ds->property["dsname"])) 
			$this->propertyJS["DSname"] = $this->ds->property["dsname"];
		$this->propertyJS["DSsavetype"] = $this->property["dssavetype"]["value"];
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		$id1 = $this->property["id"]["value"];
		global $system;
		switch($name)
		{
			case "conn":
				if (substr($this->property["conn"]["value"], 0, 4)  == "demo")
				{
					$this->property["readonly"]["value"] = "true";
					$this->propertyJS["DSreadonly"] = true;
				}
				if ($this->property["conn"]["value"] == "custom")
				{
					global $DS_CONN;
					if (!isset($DS_CONN[$this->property["id"]["value"]]))
					{
						$this->property["conn"]["value"] = null;
					 	return;
					}
					$setting = explode("|", $DS_CONN[$this->property["id"]["value"]]);
					$dshost = explode("@", $setting[1]);
					$this->setProperty("dsengine", $setting[0]);
					$this->setProperty("dshost", $dshost[0]);
					$this->setProperty("dsuser", $setting[2]);
					$this->setProperty("dspwd", $setting[3]);
					$this->setProperty("dsport", $setting[4]);
					if (isset($dshost[1]) && $this->property["dsdefault"]["value"] == null) $this->setProperty("dsdefault", $dshost[1]);
				}
				else
				{
					if (defined('_JAMP_CONNECTIONS_')) {
						$filesetting = file(_JAMP_CONNECTIONS_);
					} else {
						$filesetting = file($system->dir_real_jamp."/".$system->dir_conf."conn.inc.php");
					}
					unset($filesetting[0]);
					$find = false;
					foreach ($filesetting as $row){
						$setting = explode("|",$row);
						if ($setting[0] == $this->property["conn"]["value"])
						{
							$dshost = explode("@", $setting[2]);
							$this->setProperty("dsengine", $setting[1]);
							$h = $this->parseValue($dshost[0]);
							$this->setProperty("dshost", $h);
							$this->setProperty("dsuser", $setting[3]);
							$this->setProperty("dspwd", $setting[4]);
							$this->setProperty("dsport", $setting[5]);
							if (isset($dshost[1]) && $this->property["dsdefault"]["value"] == null) $this->setProperty("dsdefault", $dshost[1]);
							$find = true;
						}
					}
					if (!$find) ClsError::showError("DS000");
				}
			break;

			case "dsengine":
				$this->ds = $system->newDs($this->property["dsengine"]["value"]);
				$this->ds->property["id"] = $this->property["id"]["value"];
				$this->propertyJS["DSengine"] = $this->property["dsengine"]["value"];
				$this->ds->property["debug"] = $this->property["debug"]["value"];
			break;

			case "focusnew":
				$this->propertyJS["DSfocus"] = $this->property["focusnew"]["value"];
			break;

			case "focustabnew":
				$this->propertyJS["DSfocustab"] = $this->property["focustabnew"]["value"];
			break;
					
			case "readonly":
				if ($this->property["readonly"]["value"]=="true") $this->propertyJS["DSreadonly"] = true;
				else if ($this->property["readonly"]["value"]=="false") $this->propertyJS["DSreadonly"] = false;
			break;

			case "confirm":
				$this->propertyJS["DSconfirm"] = ($this->property["confirm"]["value"] == "false") ? false : true;
			break;

			case "dsorder":
				$this->propertyJS["DSorder"] = $this->property["dsorder"]["value"];
				$this->ds->property["order"] = $this->property["dsorder"]["value"];
			break;

			case "dslimit":
				$start = 0;
				if (isset($_POST["start"])) $start = $_POST["start"];
				else if (isset($_REQUEST["start"])) $start = $_REQUEST["start"];
				$this->ds->property["start"] = $start;
				$this->ds->property["end"] = $start + $this->property["dslimit"]["value"];
				$this->ds->property["limit"] = $this->property["dslimit"]["value"];
			break;

			case "store";
				$this->ds->property["store"] = $this->property["store"]["value"];
				$this->ds->dsQueryFilter("");
			break;

			case "dswhere":
				$this->ds->dsQueryFilter($this->property["dswhere"]["value"]);
			break;
		
			case "dsrefresh":
			break;

			case "dsname":
				$this->propertyJS["DSname"] = $this->property[$name]["value"];
				$this->ds->property[$name] = $this->property[$name]["value"];
			break;

			case "dskey":
				$this->propertyJS["DSkey"] = $this->property[$name]["value"];
				$this->ds->property[$name] = $this->property[$name]["value"];
			break;

			case "dsparentkey":
				$this->propertyJS["DSparentkey"] = $this->property[$name]["value"];
				$this->ds->property[$name] = $this->property[$name]["value"];
			break;
		
			case "dsreferences":
				$this->propertyJS["DSreferences"] = $this->property["dsreferences"]["value"];
				$this->ds->property["dsreferences"] = $this->property["dsreferences"]["value"];
			break;

			case "referenceskey":
				$this->propertyJS["DSreferenceskey"] = $this->property["referenceskey"]["value"];
				$this->ds->property["referenceskey"] = $this->property["referenceskey"]["value"];
			break;

			case "foreignkey":
				$this->propertyJS["DSforeignkey"] = $this->property["foreignkey"]["value"];
				$this->ds->property["foreignkey"] = $this->property["foreignkey"]["value"];
			break;

			case "deleteoncascate":
				if ($this->property["deleteoncascate"]["value"] == "true") $this->propertyJS["DSdeleteoncascate"] = true;
			break;

			case "deleteall":
				if ($this->property["deleteall"]["value"] == "true") $this->propertyJS["DSdeleteall"] = true;
			break;

			case "event":
				if ($this->property["event"]["value"] == "none")  $this->event = null;
			break;

			case "fieldencloser":
				if (empty($this->property["fieldencloser"]["value"])) $this->property["fieldencloser"]["value"] = chr(0);
			break;
			
			case "loadall":
			case "load":
			break;
			
			case "dslock":
				$this->propertyJS["dslock"] = $this->property["dslock"]["value"];
			break;

			default:
				$this->ds->property[$name] = $this->property[$name]["value"];
			break;
		}
	}

	private function _complete($string) {
		global $system;

		if (COMPRESSJS) {
			$string = $system->packJS($string);
		}
		return "<script><![CDATA[$string]]></script>\n";
	}
	
	private function unsetPost($post)
	{
		if(!empty($this->property["unsetpost"]["value"]))
		{
			$items = explode(",",$this->property["unsetpost"]["value"]);
			foreach ($items as $item)
			{
				unset($post[trim($item)]);
			}
		} 
		return $post;
	}
}
?>
