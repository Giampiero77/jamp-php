<?php
/**
* Class for managing objects
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

abstract class ClsObject 
{
	/**
	* Property
	* @var array 
	* $property = ["property"]	["value] = "text"				Value of Property
	*									["inherit"] = false/true	Inherit property from his father
	*									["html"] = false/true		HTML property
	*/
	protected $property = array(
			"debug"			=> array("value" => "false","inherit" => true,  "html" => false),
			"onclick"		=> array("value" => null, "inherit" => false, "html" => true),
			"ondblclick" 	=> array("value" => null, "inherit" => false, "html" => true),
			"onmousedown" 	=> array("value" => null, "inherit" => false, "html" => true),
			"onmouseup"		=> array("value" => null, "inherit" => false, "html" => true),
			"onmouseover" 	=> array("value" => null, "inherit" => false, "html" => true),
			"onmousemove" 	=> array("value" => null, "inherit" => false, "html" => true),
			"onmouseout" 	=> array("value" => null, "inherit" => false, "html" => true),
			"onkeypress" 	=> array("value" => null, "inherit" => false, "html" => true),
			"onkeydown"		=> array("value" => null, "inherit" => false, "html" => true),
			"onkeyup" 		=> array("value" => null, "inherit" => false, "html" => true),
			"class"			=> array("value" => null, "inherit" => false, "html" => true),
			"style" 		=> array("value" => null, "inherit" => false, "html" => true),
			"lang"			=> array("value" => null, "inherit" => true,  "html" => true),
			"dir" 			=> array("value" => null, "inherit" => true,  "html" => true),
			"template"		=> array("value" => null, "inherit" => true, "html" => false),
			"title" 		=> array("value" => null, "inherit" => false, "html" => true)
		);

	/**
	* Property Javascript
	* @var array 
	*/
	protected $propertyJS = array();

	/**
	* Container Events
	* @var array 
	*/
	protected $event = array();
	
	/**
	* Container Code JavaScript
	* @var array 
	*/
	protected $codejs;
	
	/**
	* Container JavaScript code to validate
	* @var array 
	*/
	protected $codejsvalidate;

	/**
	* Container Code JavaScript
	* @var array 
	*/
	public $multiObj = false;

	/**
	* Child object
	* @var array 
	*/
	public $isChildObj = false;

	/**
	* Performs the parsing of the value
	* @param string $value 
	* @return string
	*/
	protected function parseValue($value)
	{
		if (!is_array($value) && !is_object($value))
		{
			preg_match_all('/\$\$[\w-_]{1,}\$\$/',$value, $vars);
			foreach ($vars[0] as $var)
			{
				switch($var)
				{
					case '$$JAMP-VERSION$$':
						global $system;
						$value = str_replace($var, $system->version, $value); 
					break;
					case '$$DATETIME$$': 
						$value = str_replace($var, @date("d/m/Y H:i:s"), $value);
					break;
					case '$$DATE$$': 
						$value = str_replace($var, @date("d/m/Y"), $value); 
					break;
					case '$$DATESYS$$': 
						$value = str_replace($var, @date("Y-m-d"), $value); 
					break;
					case '$$DATETEXT$$': 
						$value = str_replace($var, @date("d F Y"), $value); 
					break;
					case '$$TIME$$': 
						$value = str_replace($var, @date("H:m:s"), $value); 
					break;
					case '$$LOGIN-USER$$':
						if(isset($_POST[$var])) $value = str_replace($var, $_POST[$var], $value);
						else if (isset($_SESSION["auth"]["user"])) $value = str_replace($var, $_SESSION["auth"]["user"], $value);
					break;
					case '$$LOGIN-DATA$$':
						if(isset($_POST[$var])) $value = str_replace($var, $_POST[$var], $value);
						else if (isset($_SESSION["auth"]["data"])) $value = str_replace($var, $_SESSION["auth"]["data"], $value);
					break;
					case '$$LOGIN-CN$$':
						if(isset($_POST[$var])) $value = str_replace($var, $_POST[$var], $value);
						else if (isset($_SESSION["auth"]["cn"])) $value = str_replace($var, $_SESSION["auth"]["cn"], $value);
					break;
					default:
						if(isset($_POST[$var])) $value = str_replace($var, $_POST[$var], $value);
					break;
				}
			} 
		} 
		return $value;
	}

	/**
	* Test Property
	* @param string $name Name property
	*/
	public function issetProperty($name)
	{
		return isset($this->property[$name]);
	}
		
	/**
	* Set the properties
	* @param string $name Name property
	* @param string $value Value property
	*/
	public function setProperty($name, $value)
	{
		if(!isset($this->property[$name])) ClsError::showError("OBJ001", $this->property["id"]["value"]." - \"".$name."\"");
		if($name == "width" || $name == "height" || $name == "colwidth")
		{
			switch(substr($value, -2))
			{	
				case "er":
				case "to":
				case "ze":
				case "px":
				case "em":
				case "ex":
				case "in":
				case "cm":
				case "mm":
				case "pt":
				case "pc":
				break;
				default:
					if(substr($value, -1) != "%") $value .= "px";
			}
		}
		$this->property[$name]["value"] = $this->parseValue($value);
		$this->setPropertyAfter($name);
	}

	/**
	* Adds code to JS for validation of data
	* @param string $code Javascript code
	*/
	public function setCodeJsValidate($code)
	{
		$this->codejsvalidate .= "\n".$code;
	}

	/**
	* Add Javascript code to object
	* @param string $code Codice JS
	*/
	public function setCodeJs($code)
	{
		$this->codejs .= "\n".$code;
	}

	/**
	* It is called after a property set
	* @param string $name Nome della proprietà
	*/
	protected abstract function setPropertyAfter($name);

	/**
	* Returns object's property
	* @param string $property Name property
	* @param boolean $error Generates the error if the property is not set
	* @return string
	*/
	public function getPropertyName($property, $error = true)
	{
		if(isset($this->property[$property])) return $this->property[$property]["value"];
		else
		{
			if($error) ClsError::showError("OBJ001", $this->property["id"]["value"]." - \"".$property."\"");
			else return null;
		}
	}

	/**
	* Returns the properties of object
	* @param string $filter Filter property. null/inherit/html/obj
	* @param boolean $isset Returns only the property set
	* @param boolean $array Returns the properties as an array
	* @return array/string
	*/
	public function getProperty($filter = null, $isset = false, $array = true)
	{
		$out = null;
		foreach($this->property as $k => $property)
		{
			if(($isset == false) || (($isset == true) && isset($property["value"])))
			{
				if(is_object($property["value"])) $value = $property["value"]->getPropertyName("id");
				else $value = $property["value"];
				if((!$array) && (is_array($value)) && ($filter != "xml")) $value = implode(",",$value);

				switch($filter)
				{
					case null:
						if($array) $out[$k] = $value;
						else $out .= "$k=\"".$value."\" ";
					break;
	
					case "inherit":
						if($property["inherit"] == true) 
							if($array) $out[$k] = $value;
							else $out .= "$k=\"".$value."\" ";
					break;
	
					case "html":
						if($property["html"] == true) 
							if($array) $out[$k] = $value;
							else $out .= "$k=\"".$value."\" ";
					break;
	
					case "xml":
						if(($k != "id") && (!is_array($value)))
							if($array) $out[$k] = $value;
							else $out .= "$k=\"".$value."\" ";
					break;
	
					case "obj":
						if($property["obj"] == false) 
							if($array) $out[$k] = $value;
							else $out .= "$k=\"".$value."\" ";
					break;
				}	
			}
		}
		if($array) return (array)$out;
		else return trim($out);
	}

	/**
	* Returns the array of properties
	* @return array
	*/
	public function getPropertyArray($filter = false)
	{
		return $this->property;	
	}
	
	/**
	* Instantiate a child object
	* @param string $id Unique name assigned to the object
	* @param string $object Object to instantiate
	* @param boolean $multiObj Container object
	*/
	public function addChild($id, $object, $multiObj = false)
	{
		global $system;
		if(!isset($this->child)) ClsError::showError("OBJ002");
		$this->child[$id] = $system->newObj($id, $object);
		$this->inheritChildProperty($this->child[$id]);
		$this->child[$id]->isChildObj = $multiObj;
		return $this->child[$id];
	}

	/**
	* Instantiate and insert the first child object of reference
	* @param string $id Unique name assigned to the object
	* @param string $object Object to instantiate
	* @param string $idrefobj $id Object reference
	* @param boolean $multiObj Container object
	*/
	public function insertBefore($id, $object, $idrefobj, $multiObj = false)
	{
		global $system;
		if(!isset($this->child)) ClsError::showError("OBJ002");
		if(!isset($this->child[$idrefobj])) ClsError::showError("OBJ008");
		foreach ($this->child as $key => $child)
		{
			if ($idrefobj == $key) $childs[$id] = $system->newObj($id, $object);
			$childs[$key] = $child;
		}
		$this->child = $childs;
		$this->inheritChildProperty($this->child[$id]);
		$this->child[$id]->isChildObj = $multiObj;
		return $this->child[$id];
	}

	/**
	* Instantiate and insert the child object after the object reference
	* @param string $id Unique name assigned to the object
	* @param string $object Object to instantiate
	* @param string $idrefobj $id Object reference
	* @param boolean $multiObj Container object
	*/
	public function insertAfter($id, $object, $idrefobj, $multiObj = false)
	{
		global $system;
		if(!isset($this->child)) ClsError::showError("OBJ002");
		if(!isset($this->child[$idrefobj])) ClsError::showError("OBJ008");
		foreach ($this->child as $key => $child)
		{
			$childs[$key] = $child;
			if ($idrefobj == $key) $childs[$id] = $system->newObj($id, $object);
		}
		$this->child = $childs;
		$this->inheritChildProperty($this->child[$id]);
		$this->child[$id]->isChildObj = $multiObj;
		return $this->child[$id];
	}

	/**
	* Delete Object
	* @param string $id Unique name assigned to the object
	*/
	public function removeChild($id)
	{
		global $system;
		if(!isset($this->child)) ClsError::showError("OBJ002");
		if(!isset($this->child[$id])) ClsError::showError("OBJ004");
		unset($this->child[$id]);
	}

	/**
	* Returns all child objects
	* @return array oggetti figli
	*/
	public function getChilden()
	{
		if (isset($this->child)) return $this->child;
		return false;
	}

	/**
	* Inherit properties from parent
	* @param string $child Child object
	*/
	protected function inheritChildProperty($child)
	{
		foreach($child->getProperty("inherit") as $k => $property)
		{
			if(isset($this->property[$k]["value"])) $child->setProperty($k,$this->property[$k]["value"]);
		}
	}

	/**
	* List property subject
	* @param boolean $outArray Return the property in an array
	* @param string $outSeparator Separator property
	*/
	public function enumProperty($outArray = false, $outSeparator = ",")
	{
		$enum = array_keys($this->property);
		if(!$outArray) $enum = implode($outSeparator, $enum);
		return $enum;
	}

	/**
	* Generate and print the html code for generating the object
	* @param string $jsextra Javascript to add extra
	*/
	public function printOUT($jsextra = null)
	{
		global $system, $xml;
		switch($this->property["out"]["value"])
		{
			case "text":
				header('Content-Disposition: attachment; filename="export.txt"');
				$this->codeTXT();
			break;

			case "xls":
				$xml_ds = new ClsXML($xml->filexml, $xml->typexml);
				$xml_ds->getElementsByTagName('ds');
				$filename = $xml_ds->pageObj->getPropertyName("title");
				$filename = (empty($filename)) ? "export.xls" : $filename.".xls";
				header('Content-Disposition: attachment; filename="export.xls"');
				$ds_obj = $xml_ds->pageObj->child;
				foreach ($ds_obj as $dsobjname => $dsobj)
				{
					$dsobj->ds->dsConnect();
					$qry = $dsobj->getPropertyName("dsquery_select");
					$return = $dsobj->callEvent("data_select_before", $dsobj);
					if(is_null($return) || ($return == true))
					{
						if (empty($qry)) $dsobj->ds->dsQuerySelect();
						else $dsobj->ds->dsQuerySelect($qry);
					}
					$dsobj->callEvent("data_select_after", $dsobj);
				}
				$this->printGraph();
				$code = "<table><tr>";
				$code .= $this->codeXLS();
				$code .= "</tr></table>";
				print $code;
			break;

			case "php":
				$this->codePHP();
			break;
					
			case "pdf":
				$xml_ds = new ClsXML($xml->filexml, $xml->typexml);
				$xml_ds->getElementsByTagName('ds');
				$ds_obj = $xml_ds->pageObj->child;
				foreach ($ds_obj as $dsobjname => $dsobj)
				{
				  $dsobj->ds->dsConnect();
				  $qry = $dsobj->getPropertyName("dsquery_select");
				  $return = $dsobj->callEvent("data_select_before", $dsobj);
				  if(is_null($return) || ($return == true)) 
				  {
						if (empty($qry)) $dsobj->ds->dsQuerySelect();
						else $dsobj->ds->dsQuerySelect($qry);
				  }
				  $dsobj->callEvent("data_select_after", $dsobj);
				}
				$this->printGraph();
				$this->codePDF(null);
			break;

			default:	// html
				if (NOCACHEPHP)
				{
					header("Cache-Control: no-cache, must-revalidate");
					header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 
					header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				}
				$code  = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";
				$code .= "\n<html>";
				$code .= $this->codeHTML();
				if ($xml->pageObj->isJampEngineActivated()) {
					if($this->property["debug"]["value"] == "true") $jsextra .= "\n".$system->sendDebug(); //DEBUG
					$jsextra = $this->codeJavaScript().$jsextra;
					if(!empty($jsextra)) $code .= $system->setJs($jsextra);
				}
				$code .= "\n</body>";
				$code .= "\n</html>";
				if (COMPRESSHTML)
				{
					require_once("compress.class.php");
					$compress = new clsCompress();
					$code = $compress->gzPost($code);
				}
				print $code;
			break;
		}
	}

	/**
	* Generate the XML data of the DS
	* @param boolean $filterds filter object ds
	*/
	public function codeDsXML($filterds = false)
	{
		global $xml;
		$code = "";
		if(isset($this->child)) 
		{
			if (!$filterds) foreach($this->child as $obj) $code .= $obj->codeDsXML();
			else
			{
				$filter = explode(",",$filterds);
				foreach($filter as $objname) 
				{
					$obj = $xml->pageObj->child[$objname];
					$code .= $obj->codeDsXML();
				}
			}
		}
		return $code;
	}

	/**
	* Genera i grafici inseriti nella pagina
	*/
	public function printGraph()
	{
		$code = "";
		global $system, $xml, $event;
		$xml_graph = new ClsXML($xml->filexml, $xml->typexml);
		$xml_graph->getElementsByTagName('graphic');
		$graphics = $xml_graph->pageObj->child;
		if (count($graphics)>0)
		{
			ob_start();
			require_once($system->dir_real_jamp."/".$system->dir_class.'graph.class.php');
			ob_end_clean();
			$js = "";
			$graphs = null;
			$return = $event->callEvent("start_graph", $xml_graph);
			if (is_null($return) || ($return == true))
			{
				foreach ($graphics as $id => $graphic) 
				{
					$clsgraph = new clsGraphics($graphic, $graphs);
					$graphs[$id] = $clsgraph->Paint($system->dir_real_web.$graphic->getPropertyName("path"));
					if ($clsgraph->refresh) $js .= "\nGRAPHIC.getDsValue(\"".$id."\");";
				}
			}
			$event->callEvent("end_graph", $graphs);
			if ($js!="") 
			{
				$code .= "<script>\n";
				$code .= "<![CDATA[";
				$code .= $js;
				$code .= "\n]]>\n";
				$code .= "</script>\n";
			}
		}
		return $code;
	}

	/**
	* Print the XML data of the DS
	* @param boolean $filterds filter object ds
	*/
	public function printXML($filterds = false)
	{
		global $system, $event;
		$code = "";
 		$code .= $this->codeDsXML($filterds);
		$codejs = $event->getCodeJs();
		if(!empty($codejs))
		{
			$code .= "<script>\n";
			$code .= "<![CDATA[";
			if (COMPRESSJS) {
				$code .= $system->packJS($codejs);
			} else {
				$code .= $codejs;
			}
			$code .= "]]>\n";
			$code .= "</script>\n";
		}
		if($this->property["debug"]["value"] == "true")
		{
			$code .= "<script>\n";
			$code .= "<![CDATA[";
			$code .= $system->sendDebug();
			$code .= "\n]]>\n";
			$code .= "</script>\n";
		}
 		$code .= $this->printGraph();
		if (!empty($this->property["extraxml"]["value"])) $code .= $this->property["extraxml"]["value"];
		if (!empty($code))
		{
			@header('Content-type: application/xml; charset="utf-8"',true);
			$code = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<data>\n$code</data>\n";
		}
		if (COMPRESSXML) 
		{
			require_once("compress.class.php");
			$compress = new clsCompress();
			$code = $compress->gzPost($code);
		}
		print $code;	
	}

	/**
	* Require file JavaScript
	* @return array
	*/
	public function requireJavaScript()
	{
		$js = Array();
		if(isset($this->child))
		{
			if(isset($this->property["java"]["value"]))
				if(is_array($this->property["java"]["value"])) $js = $this->property["java"]["value"];
				else $js[] = $this->property["java"]["value"];
			foreach($this->child as $obj)
			{
				$jschild = $obj->requireJavaScript();
				if(is_array($jschild)) $js = array_merge($js, $jschild);
			}
			return $js;
		} else if(isset($this->property["java"]["value"])) return (array)$this->property["java"]["value"];
	}

	/**
	* Require file CSS
	* @return array
	*/
	public function requireCSS()
	{
		$css = Array();
		if(isset($this->child))
		{
			if(isset($this->property["cssfile"]["value"])) 
			{
				if (is_array($this->property["cssfile"]["value"])) $css = $this->property["cssfile"]["value"];
				else $css[] = $this->property["cssfile"]["value"];
			}
			foreach($this->child as $obj)
			{
				$csschild = $obj->requireCSS();
				if(is_array($csschild)) $css = array_merge($css, $csschild);
			}
			return $css;
		} else if(isset($this->property["cssfile"]["value"])) return (array)$this->property["cssfile"]["value"];
	}

	/**
	* Returns the javascript code
	* @return array
	*/
	public function codeJavaScript()
	{
		$code = "";
		if(isset($this->property["id"]["value"])) 
		{
			$id = $this->property["id"]["value"];
			$this->propertyJS["typeObj"] = str_replace("ClsObj_","",  get_class($this));
			if(isset($this->property["dsobj"]["value"]))
			{
				$this->propertyJS["dsObj"] = $this->property["dsobj"]["value"];
				if($this->isChildObj == false) $this->propertyJS["isParentObj"] = true;
			}
			if(isset($this->property["dsitem"]["value"])) $this->propertyJS["dsItem"] = $this->property["dsitem"]["value"];
			//OUT PROPERTY JS
			global $system;
			require_once($system->dir_real_jamp."/".$system->dir_class."/json.php");
			$json = new Services_JSON();
			$code .="\n\t\t$('$id').p = {";
			$txt = array();
			foreach ($this->propertyJS as $key => $value) $txt[] = $key.":".$json->encode($value);
			$code .= implode(",", $txt)."};";
		}
		$code .= $this->codejs;
		if(isset($this->child)) foreach($this->child as $obj) $code .= $obj->codeJavaScript();
		return $code;
	}
	
	/**
	* Returns
	*/
	public function getEvent()
	{
		if(isset($this->child))
		{
			$event = $this->event;
			foreach($this->child as $obj)
			{
				$eventchild = $obj->getEvent();
				if(is_array($eventchild)) $event = array_merge_recursive($event,$eventchild);
			}
			return $event;
		} 
		else return $this->event;
	}

	/**
	* Returns the code for the validation
	*/
	public function getValidate()
	{
		if(isset($this->child))
		{
			$code = $this->codejsvalidate;
			foreach($this->child as $obj) $code .= $obj->getValidate();
			return $code;
		} 
		else return $this->codejsvalidate;
	}

	/**
	* Generate the html to create the object
	*/
	public abstract function codeHTML();

	/**
	* Txt generates code to create the object
	*/
	public abstract function codeTXT();

	/**
	 * SCRIPT generates code to create the object
	 */
	public function codePHP()
	{
		
	}
	
	/**
	* Generate the code to generate the pdf object
	*/
	public abstract function codePDF($pdf);

	/**
	 * Generate the code to generate the xls object
	 */
	public function codeXLS()
	{
		$code = "";
		if (isset($this->child)) foreach ($this->child as $obj) $code .= $obj->codeXLS();
		else $code = $this->codeTXT();
		return $code;	
	}
	
	/**
	* Generates the code of the xml
	*/
	public function codeXML($tab = ""){
		$tag = "";
		if (!isset($this->child)) $tag = "/"; 
		return "$tab<".$this->getPropertyName("id")." typeobj=\"".get_class($this)."\" ".$this->getProperty("xml", true, false)."$tag>\n";
	}

	/**
	* Control object type
	* @return boolean if the object is required
	*/
	public function isObj($test)
	{
		return "ClsObj_$test" == get_class($this);
	}

	/**
	* Set a event listener
	* @param $id Object
	* @param $event Event
	* @param $function Function event
	*/
	public function addEventListener($id, $event, $function, $run = false, $param = null)
	{
		if (!isset($_SERVER["HTTP_USER_AGENT"])) return;
		$out = "$(\"$id\")";

		if($id == "window" || $id == "document") $out = $id;

		if ((strpos($_SERVER["HTTP_USER_AGENT"], "MSIE") === false) && (strpos($_SERVER["HTTP_USER_AGENT"], "Opera") === false))
			$out .= ".addEventListener(\"$event\", $function, true);";
		else 
		{ //IE EVENT
			$out .= ".attachEvent(\"on$event\", $function);";
      }
		if($run) $out .= " $function();";
		$this->event[$function]["event"][] = $out;
		$this->event[$function]["param"] = $param;
	}

	/**
	* setCSS property
	* @return $class classname
	*/
	public function setCSS()
	{
		if (!empty($this->property["template"]["value"]))
		{
			$objname = substr(get_class($this), 7);
			$template = $this->property["template"]["value"];
			$this->property["class"]["value"] = $objname."_".$template;
			$this->property["cssfile"]["value"] = "objcss/$template/$objname.css";
		}
		else if (!isset($this->property["cssfile"]["value"]))
		{
			$objname = substr(get_class($this), 7);
			$this->property["class"]["value"] = $objname."_".TEMPLATE;
			$this->property["cssfile"]["value"] = "objcss/".TEMPLATE."/$objname.css";
		}
		return $this->property["class"]["value"];
	}

	/**
	* Function to label align(left - right)
	* @param $code Object code
	* @param $tab tab form impagination
	*/
	public function getCodeLabelAlign($code, $tab)
	{
		$codelabel = $stylelabel = "";
		$id = $this->property["id"]["value"];
		$labelalign = $this->property["labelalign"]["value"];
		$labelwidth = $this->property["labelwidth"]["value"];
		$labelstyle = $this->property["labelstyle"]["value"];
		if (!empty($this->property["label"]["value"]))
		{
			$label = str_replace('\n',"<br>", $this->property["label"]["value"]);
			if (!empty($labelstyle)) $stylelabel = " style=\"$labelstyle\"";	
			$codelabel = "<label$stylelabel>$label</label>";
			if (!empty($labelwidth))
			{
				if ($labelalign=="left") $code = "\n".$tab."<div style=\"float:left;width:".$labelwidth.";\">$codelabel</div>$code";
 				else $code = "\n".$tab."<div style=\"float:left;width:".$labelwidth.";\">$code</div><div style=\"float:left;\">$codelabel</div>";
				if (!$this->isChildObj)	$code = "\n$tab<div id=\"".$id."_container\" style=\"width:100%;\">".$code."\n$tab</div>";
			}
		 	else
			{
				if ($labelalign=="left") $code = "\n".$tab.$codelabel.$code;
 				else $code = "\n".$tab.$code.$codelabel;
				if (!$this->isChildObj)	$code = "\n$tab<div style=\"display:inline;\" id=\"".$id."_container\">".$code."\n".$tab."</div>";
			}
		}
		else $code = $tab.$code;
		return $code;
	}
	
	/**
	* function associated with a event
	* @param $id Object
	* @param $function Function called by event
	* @param $call Functions to call
	*/
	public function addEvent($id, $function, $call)
	{
		$this->event[$function]["function"][] = $call;
	}

	/**
	* Associated with function to be executed after all other
	* @param $id Object
	* @param $function Function called by event
	* @param $call Functions to call
	*/
	public function addEventAfter($id, $function, $call)
	{
		$this->event[$function]["after"][] = $call;
	}

	/**
	* Function associated with to do before all other
	* @param $id Object
	* @param $function Function called by event
	* @param $call Functions to call
	*/
	public function addEventBefore($id, $function, $call)
	{
		$this->event[$function]["before"][] = $call;
	}

	/**
	* Deletes the object even
	*/
	public function removeAllEvent()
	{
		$this->event = array();
	}

	/**
	* Build the object
	*/
	public function BuildObj()
	{
	}

	/**
	* Funzione di refresh dell'oggetto
	*/
	public function refreshOBJ()
	{
	}
}
?>
