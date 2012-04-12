<?php
/**
* Class management event handling and html requests
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsEvent
{
	/**
	* Container Code JavaScript
	* @var array 
	*/
	protected $codejs;

	/**
	* Link class xml
	* @var array 
	*/
	protected $xml;

	/**
	* Construct
	* @param class $xml class xml
	*/
	public function __construct(& $xml, $obj=null){
		if (is_object($obj)) userEvent::setClassObject($obj);
		$this->xml = $xml;
	}

	/**
	* Call Event
	* @param string $event event
	* @param string $param parameters
	*/
	function callEvent($event, $param = null)
	{
		return userEvent::call($event, $param);
	}

	/**
	* Adds javascript code to object
	* @param string $code javascript code
	*/
	public function setCodeJs($code)
	{
		$this->codejs .= $code."\n";
	}

	/**
	* return javascript code
	* @return string $code javascript code
	*/
	public function getCodeJs()
	{
		return $this->codejs;
	}

	/**
	* Manages the REQUEST
	* @param object $xml Oggetto XML
	*/
	function managerRequest()
	{
		$returnxml = true;
		if(isset($_POST["returnxml"])) 
		{
			$returnxml = false;
			unset($_POST["returnxml"]);
		}
		if(isset($_POST["multirequest"])) // Multi Request
		{
			$totrequest = $_POST["multirequest"];
			$_POST_SAVE = $_POST;
			for($i = 0; $i < $totrequest; $i++)
			{
				$POST = "";
				foreach($_POST_SAVE as $item => $value)
				{
					if (is_array($value) && isset($value[$i])) $POST[$item] = $value[$i];
					else if(!is_array($value) && $item != "multirequest") $POST[$item] = $value;
				}
				$_POST = $POST;
				$ret = false;
				$lastmulti = false;
				if($i == ($totrequest-1))
				{
					$lastmulti = true;
					$ret = true;
					if($returnxml == false) $ret = false; 
				}
				$this->sendRequest($ret, $lastmulti, true);
			}
		}
		else $this->sendRequest($returnxml);
	}

	/**
	* Rewrite object
	*/
	function rewriteOBJ()
	{
		global $system;
		$time = (NOCACHECSS) ? "?".NOCACHECSS : "";
		$this->callEvent("html_before_rewrite");
		$obj = $_POST['objname'];
		$this->xml->getElementsAllTag(false);
		$res = $this->xml->getObjById($obj);
		$this->callEvent("html_rewrite", $res);
		$this->xml->BuildObjects();
		if (!$res->isObj("page"))
		{
			$cssfiles = $this->xml->pageObj->requireCSS();
			$jsfiles = array_unique($this->xml->pageObj->requireJavaScript());
	
			$this->codejs .= "\n\t\tSYSTEMBROWSER.manageCSS(['".implode("','", $cssfiles)."'], '".$time."', '".$system->dir_web_jamp.$system->dir_template."');";
			$this->codejs .= "\n\t\tSYSTEMBROWSER.manageJS(['".implode("','", $jsfiles)."'], '".$time."', '".$system->dir_web_jamp.$system->dir_js."');";

			$this->codejs .= "\n".$res->refreshOBJ();
			$this->returnRequestRewrite($obj, $res->codeJavaScript()."\n".$this->codejs, $res->codeHTML());
		} 
		else $res->printOUT();
		$this->callEvent("html_after_rewrite");
	}

	function addSlashes($array)
	{
		foreach($array as $item => $value) 
		{
			if (is_array($value)) 
			{
				foreach($value as $item2 => $value2) $array[$item][$item2] = addslashes($value2);
			}
			else $array[$item] = addslashes($value);
		}
		return $array;
	}


	/**
	* Process REQUEST
	* @param string $returnxml Datasource filter
	* @param string $lastmulti Indicates whether the last request for a multi-post
	*/
	function sendRequest($returnxml, $lastmulti = false, $multi = false)
	{
		if (!get_magic_quotes_gpc())
		{
			$_REQUEST = $this->addSlashes($_REQUEST);
			$_POST = $this->addSlashes($_POST);
			$_GET = $this->addSlashes($_GET);
		}
		$objname = isset($_POST['dsobjname']) ? $_POST['dsobjname'] : false;
		if(isset($_POST["data"]))
		{	
			$this->callEvent("data_before");
			$return = $this->callEvent("data");
			if(is_null($return) || ($return == true))
			{
				switch ($_POST["data"])
				{
					case "keepalive":
						$this->callEvent("data_keepalive");
					break;

					case "loadall":
						$this->callEvent("data_before_loadall");
						$return = $this->callEvent("data_loadall");
						if(is_null($return) || ($return == true))
						{
							$this->xml->getElementsByTagName('ds');
							$this->callEvent("data_after_loadall");
						}
						$objname = false;
					break;
	
					case "login":
						$this->callEvent("data_before_login");
						$this->xml->getElementById($objname);
						$res = $this->xml->getObjById($objname);
						$return = $this->callEvent("data_login", $res);
						if(is_null($return) || ($return == true))
						{
							$res->dsLogin($_POST);
							$this->callEvent("data_after_login", $res);
						}
					break;
			
					case "changepasswd":
						$this->callEvent("data_before_changepasswd");
						$this->xml->getElementById($objname);
						$res = $this->xml->getObjById($objname);
						$return = $this->callEvent("data_changepasswd", $res);
						if(is_null($return) || ($return == true))
						{
							$res = $res->dsChangePasswd($_POST);
							$this->callEvent("data_after_changepasswd", $res);
						}
					break;
			
					case "load":
						$this->callEvent("data_before_load");
						$return = $this->callEvent("data_load");
						if(is_null($return) || ($return == true))
						{
							$this->xml->getElementsByTagName('ds');
							$this->callEvent("data_after_load");
						}
					break;
			
					case "update":
						$this->callEvent("data_before_update");
						$this->xml->getElementById($objname);
						$res = $this->xml->getObjById($objname);
						$return = $this->callEvent("data_update", $res);
						if(is_null($return) || ($return == true))
						{
							$res->dsUpdate($_POST);
							$this->callEvent("data_after_update", $res);
							$ghost = $res->getPropertyName("ghostdata");
							$ghost = empty($ghost) ? GHOSTDATA : $ghost;
							if (($ghost=="true"||$ghost===true) && ($multi==false||($multi==true&&$lastmulti==true))) $this->codejs .= "\nSYSTEMEVENT.showMessageGhost('".LANG::translate("MSG1HOST")."','".LANG::translate("MSG1HOSTD")."');";
						}
					break;
			
					case "new":
					case "new_update":
						$this->callEvent("data_before_new");
						$this->xml->getElementById($objname);
						$res = $this->xml->getObjById($objname);
						$return = $this->callEvent("data_new", $res);
						if(is_null($return) || ($return == true))
						{
							$res->dsNew($_POST, $_POST["data"]=="new_update");
							$this->callEvent("data_after_new", $res);
							$ghost = $res->getPropertyName("ghostdata");
							$ghost = empty($ghost) ? GHOSTDATA : $ghost;
							if (($ghost=="true"||$ghost===true) && ($multi==false||($multi==true&&$lastmulti==true))) $this->codejs .= "\nSYSTEMEVENT.showMessageGhost('".LANG::translate("MSG2HOST")."','".LANG::translate("MSG2HOSTD")."');";
						}
					break;
			
					case "delete":
						$this->callEvent("data_before_delete");
						$this->xml->getElementById($objname);
						$res = $this->xml->getObjById($objname);
						$return = $this->callEvent("data_delete", $res);
						if(is_null($return) || ($return == true))
						{
							$res->dsDelete($_POST);
							$this->callEvent("data_after_delete", $res);
							$ghost = $res->getPropertyName("ghostdata");
							$ghost = empty($ghost) ? GHOSTDATA : $ghost;
							if (($ghost=="true"||$ghost===true) && ($multi==false||($multi==true&&$lastmulti==true))) $this->codejs .= "\nSYSTEMEVENT.showMessageGhost('".LANG::translate("MSG3HOST")."','".LANG::translate("MSG3HOSTD")."');";
						}
					break;
	
					case "deleteall":
						$this->callEvent("data_before_deleteall");
						$this->xml->getElementById($objname);
						$res = $this->xml->getObjById($objname);
						$return = $this->callEvent("data_deleteall", $res);
						if(is_null($return) || ($return == true))
						{
							$res->dsDeleteAll();
							$this->callEvent("data_after_deleteall", $res);
							$ghost = $res->getPropertyName("ghostdata");
							$ghost = empty($ghost) ? GHOSTDATA : $ghost;
							if (($ghost=="true"||$ghost===true) && ($multi==false||($multi==true&&$lastmulti==true))) $this->codejs .= "\nSYSTEMEVENT.showMessageGhost('".LANG::translate("MSG3HOST")."','".LANG::translate("MSG3HOSTD")."');";
						}
					break;
	
					case "store":
						$this->callEvent("data_before_store");
						$return = $this->callEvent("data_store");
						if(is_null($return) || ($return == true))
						{
							$_SESSION["store"][$_POST['dsobjname']]['keyname'] = $_POST['keyname'];
							$_SESSION["store"][$_POST['dsobjname']]['keyvalue'] = $_POST['keyvalue'];
							$this->callEvent("data_after_store");
							$this->returnRequest("", $this->codejs);
						}
						return;
					break;
					default:
						if (!isset($this->xml->pageObj)) $this->xml->LoadXMLFromFile();
						$return	= $this->callEvent($_POST["data"]);
 						if (!is_null($return) && ($return)==false) return;
				}
				if($lastmulti == true) $this->callEvent("data_last_multirequest");
				$this->callEvent("data_after");
				if ($returnxml) $this->xml->pageObj->printXML($objname);
			}
		} else {
			if (isset($_POST['objname'])) $this->rewriteOBJ();
			else 
			{
				$this->callEvent("html_before_load");
				$this->xml->getElementsAllTag();
				$this->callEvent("html_load");
				$this->xml->pageObj->printOUT($this->codejs);
				$this->callEvent("html_after_load");
			}
		}
	}

	/**
	* Returns the response code to a request
	* @param array $data array of data
	* @param array $js array of javascript
	* @param string $html html code
	*/
	function returnRequest($data, $js = null, $html = null)
	{
		@header('Content-type: application/xml; charset="utf-8"',true);
		print "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		if(!empty($js)) print "\t$html\n";
		print "<data>\n";
		if(!empty($js))
		{
			print "\t<script>\n";
			print "\t<![CDATA[";
			print "\t".$js;
			print "\n\t]]>\n";
			print "\t</script>\n";
		}
		if(!empty($data)) print $data;
		print "</data>\n";
	}

	/**
	* Returns the response code to a request rewrite
	* @param array $data array of data
	* @param array $js array of javascript
	* @param string $html html code
	*/
	function returnRequestRewrite($id, $js, $html)
	{
		@header('Content-type: application/xml; charset="utf-8"',true);
		print "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		print "<data>\n";
		print "\t<html id=\"$id\">\n";
		print "\t<![CDATA[";
		print "\t".$html;
		print "\n\t]]>\n";
		print "\t</html>\n";
		if(!empty($js))
		{
			print "\t<script>\n";
			print "\t<![CDATA[";
			print "\t".$js;
			print "\n\t]]>\n";
			print "\t</script>\n";
		}
		print "</data>\n";
	}
}
?>