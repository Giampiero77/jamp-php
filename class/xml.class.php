<?php
/**
* Class ClsXML
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsXML {
	public $pageObj;
	public $isJamp = true;
	public $filexml;
	public $typexml = null;
	private $ObjConn;
	private $linkobj;
	public $xmlpage = null;

	/**
	* Costruttore
	*/
	public function __construct($input = null, $type = "file")
	{
		$this->filexml = $input;
		if (is_array($this->filexml)) $input = $this->filexml[0];
		$this->pageObj	= null;
		$this->ObjConn  = null;
		$this->linkobj	= array();
		$this->typexml	= $type;
		try 
		{
			switch($type)
			{
				case "file":
					if (defined('JAMP_USER_XML_FUNCTION')) {
						$func = JAMP_USER_XML_FUNCTION;
						$this->xmlpage = simplexml_load_string ($func ($input)) or ClsError::showError("XML001");
					} else {
						$this->xmlpage = simplexml_load_file($input) or ClsError::showError("XML001");
					}
				break;
				case "string":
					$this->xmlpage = simplexml_load_string($input) or ClsError::showError("XML001");
				break;
				default:
					ClsError::showError("XML000");
				break;
			}
		}  
		catch (Exception $e) 
		{  
		     ClsError::showError("XML001");
		}  
		if (!isset($this->xmlpage->page)) $this->isJamp = false;
	}

	/**
	* Distruttore
	*/
	public function __destruct() 
	{
		$this->pageObj = null;
		$this->ObjConn = null;
		$this->linkobj = null;
	}

	/**
	* set subproperties
	* @param object $node node XML
	* @param object $obj object instantiated from the node
	* @return object 
	*/
	private function setSubAttributes($node, $obj) 
	{
		if (count($node)>0) 
		{
			if (isset($node->children()->attributes()->id)) return $node;
			foreach ($node->children() as $childnode) 
			{	
				$attr = array(); 
				$j=0;
				if (isset($childnode->children()->isChild)) 
				{
					foreach ($childnode->children() as $attribute => $value) 
					{
						$attr[$attribute][$j]=(string)$value;
					}
					$j++;
				}
				else
				{	
					$i=0; 
					foreach ($childnode->children() as $typeobj => $child) 
					{
						foreach ($child->attributes() as $attribute => $value) 
						{
							if($attribute == "dsitem" && $value!="")
							{
								switch($value)
								{
									case "data":
									case "dsobjname":
									case "start":
									case "keyname":
									case "keynamevalue":
										ClsError::showError("OBJ005", $value);
									break;
									default:
										if (!preg_match("/^[A-Za-z0-9_\-,àéèìùò]+$/", $value)) ClsError::showError("OBJ009", $obj->getPropertyName("id"), $value);
								}
							}
							$attr[$attribute][$i]=(string)$value;
						}
						$attr["objtype"][$i]=$typeobj;
						$i++;
					}
				}
				foreach ($attr as $attribute => $value)
				{
					$obj->setProperty($attribute, $value);
				}
				if(isset($child)) $node = $child;
				else 
				{
					$node = $childnode->children();
					$node->isChild = true;
				}
			}
		}
		return $node;
	}

	/**
	* set properties
	* @param object $node node XML
	* @param object $obj object instantiated from the node
	* @return object 
	*/
	private function setAttributes($node, $obj) 
	{
		foreach ($node->attributes() as $attribute => $value) 
		{
			if ($attribute == "dsitem" && $value!="")
			{
				switch($value)
				{
					case "data":
					case "dsobjname":
					case "start":
					case "keyname":
					case "keynamevalue":
						ClsError::showError("OBJ005", $value);
					break;
					default:
						if (!preg_match("/^[A-Za-z0-9_\-,àéèìùò]+$/", $value)) ClsError::showError("OBJ009", $obj->getPropertyName("id"), $value);
				}
			}
			$obj->setProperty($attribute, (string)$value);
		}
	}

	/**
	* Memorize the position of an object created manually
	* @param object $obj object
	* @param object $type object type
	*/
	public function setLinkObj($id, $obj, $type)
	{
		$this->linkobj[$id]["obj"] = $obj;
		$this->linkobj[$id]["type"] = $type;
	}

	/**
	* Reads the contents of an XML file and load it into the pageObj structure 
	* @return object $node returns the first page node
	*/	
	public function LoadXMLFromFile() 
	{
		$this->linkobj = Array();
		global $system;
		$node = $this->xmlpage->page;
		$this->pageObj = $system->newObj((string)$node->attributes()->id, "page");
		$this->setLinkObj((string)$node->attributes()->id, $this->pageObj, "page");
		$this->setAttributes($node, $this->pageObj);
		return $node;	
	}

	/**
	* Recall Method BuildObj of all objects instantiated
	*/	
	public function BuildObjects() 
	{
		foreach($this->linkobj as $obj) $obj["obj"]->BuildObj();
	}

	/**
	* Recursive method loads for XML structure nodes in to pageObj structure
	* @param object $xml XML contents
	* @param object $parent parent object
	*/
	private function ReadAllNodes($xml, $parent) 
	{
		global $system;
		foreach($xml->children() as $type => $node) 
		{
			if ($type=="xmlpage") 
			{
				$src = userEvent::call("xmlpage_event", (string)$node->attributes()->src);
				if ($src!=1) $linkpage = new ClsXML($src);
				else $linkpage = new ClsXML((string)$node->attributes()->src);
				$this->setAttributes($linkpage->xmlpage->page, $this->pageObj);
				$this->ReadAllNodes($linkpage->LoadXMLFromFile(), $parent);
			}
			else
			{
				$obj = $parent->addChild((string)$node->attributes()->id,$type);
				if (isset($this->linkobj[(string)$node->attributes()->id])) ClsError::showError("OBJ000", (string)$node->attributes()->id);
				$this->setLinkObj((string)$node->attributes()->id, $obj, $type);
				$this->setAttributes($node, $obj);
				$node = $this->setSubAttributes($node, $obj);
				if($obj->getPropertyName("debug",false) == "true")
				{
					$system->Debug((string)$node->attributes()->id,"<font color=\"green\">Read OBJ from XML<\/font>");
					$system->Debug((string)$node->attributes()->id,"<font color=\"green\">Read Property OBJ<\/font> ".$obj->getProperty(null, true, false));	
				}
				$this->ReadAllNodes($node, $obj);
			}
		}
	}

	/**
	* Reads XML file and load it into the pageObj structure
	*/	
	public function getElementsAllTag($build = true) 
	{
		$node = $this->LoadXMLFromFile();
		$this->ReadAllNodes($node, $this->pageObj);
		if (is_array($this->filexml)) $this->overrideTags();
		if ($build) $this->BuildObjects();
	}

	/**
	* Recursive method loads for XML structure nodes in to pageObj structure where typeobj=$typeobj
	* @param object $xml XML contents
	* @param object $typeobj object type
	*/
	private function ReadTagNameNodes($xml, $typeobj) 
	{
		foreach($xml->children() as $type => $node) 
		{
			if ($type=="xmlpage") 
			{
				$src = userEvent::call("xmlpage_event", (string)$node->attributes()->src);
				if ($src!=1) $linkpage = new ClsXML($src);
				else $linkpage = new ClsXML((string)$node->attributes()->src);				
				$this->setAttributes($linkpage->xmlpage->page, $this->pageObj);
				$this->ReadTagNameNodes($linkpage->LoadXMLFromFile(), $typeobj);	
			}
			else if ($type == $typeobj) 
			{
				$obj = $this->pageObj->addChild((string)$node->attributes()->id,$type);
				if (isset($this->linkobj[(string)$node->attributes()->id])) ClsError::showError("OBJ000", (string)$node->attributes()->id);
				$this->setLinkObj((string)$node->attributes()->id, $obj, $type);
				$this->setAttributes($node, $obj);
				$node = $this->setSubAttributes($node, $obj);
			}
			$this->ReadTagNameNodes($node, $typeobj);
		}
	}

	/**
	* Reads the contents of an XML file and load it into the structure pageObj
	* @param string $filename filename
	* @param string $typeobj load the specified items as children of all pageObj
	*/	
	public function getElementsByTagName($typeobj) 
	{
		$node = $this->LoadXMLFromFile();
		$this->ReadTagNameNodes($node, $typeobj);
		if (is_array($this->filexml)) $this->overrideTags();
		$this->BuildObjects();
	}

	/**
	* Recursive method loads for XML structure nodes in to pageObj structure where id=$idobj
	* @param object $xml XML contents
	* @param object $idobj object id
	*/
	private function ReadTagIdNodes($xml, $idobj) 
	{
		foreach($xml->children() as $type => $node) 
		{
			if ($type=="xmlpage") 
			{
				$src = userEvent::call("xmlpage_event", (string)$node->attributes()->src);
				if ($src!=1) $linkpage = new ClsXML($src);
				else $linkpage = new ClsXML((string)$node->attributes()->src);
				$this->setAttributes($linkpage->xmlpage->page, $this->pageObj);
				$this->ReadTagIdNodes($linkpage->LoadXMLFromFile(), $idobj);
			}
			else if ((string)$node->attributes()->id == $idobj) 
			{
				$obj = $this->pageObj->addChild((string)$node->attributes()->id,$type);
				if (isset($this->linkobj[(string)$node->attributes()->id])) ClsError::showError("OBJ000", (string)$node->attributes()->id);
				$this->setLinkObj((string)$node->attributes()->id, $obj, $type);
				$this->setAttributes($node, $obj);
				$node = $this->setSubAttributes($node, $obj);
				return;
			}
			$this->ReadTagIdNodes($node, $idobj);
		}
	}

	/**
	* Reads the contents of an XML file and load it into the structure pageObj
	* @param string $filename filename
	* @param string $idsobj load the specified items as children of all pageObj
	*/	
	public function getElementById($idsobj) 
	{
		$node = $this->LoadXMLFromFile();
		$ids = explode(",", $idsobj);
		foreach ($ids as $id) $this->ReadTagIdNodes($node, $id);
		if (is_array($this->filexml)) $this->overrideTags();
		$this->BuildObjects();
	}

	/**
	* Recursive method to override the nodes instantiated
	* @param string $xml contents of XML file
	*/
	private function overrideNodes($xml) 
	{
		global $system;
		foreach($xml->children() as $type => $node) 
		{
			if ($type=="xmlpage") 
			{
				$src = userEvent::call("xmlpage_event", (string)$node->attributes()->src);
				if ($src!=1) $linkpage = new ClsXML($src);
				else $linkpage = new ClsXML((string)$node->attributes()->src);				
				$this->setAttributes($linkpage->xmlpage->page, $this->pageObj);
				$this->overrideNodes($linkpage->LoadXMLFromFile());
			}
			else
			{
 				if (isset($this->linkobj[(string)$node->attributes()->id])) 
				{
					$obj = $this->linkobj[(string)$node->attributes()->id]["obj"];
					$this->setAttributes($node, $obj);
					$node = $this->setSubAttributes($node, $obj);
					$this->overrideNodes($node, $obj);
				}
			}
		}
	}

	/**
	* Reads the contents of an XML file and load it into the structure pageObj
	*/	
	public function overrideTags() 
	{
		for ($i=1; $i<count($this->filexml); $i++)
		{
			$ovveridepage = new ClsXML($this->filexml[$i], $this->typexml);
			$node = $ovveridepage->LoadXMLFromFile();
			$this->overrideNodes($node);
		}
	}

	/**
	* Return the objects instantiated based on id
	* @param string $id
	* @return array $ret
	*/
	public function getObjById($id)
	{
		if(isset($this->linkobj[$id]["obj"])) return $this->linkobj[$id]["obj"];
		else return null;
	}

	/**
	* Return the objects instantiated based on type
	* @param string $type
	* @return array $ret
	*/
	public function getObjByType($type)
	{
		$ret = array();
		foreach($this->linkobj as $id => $obj)
		{
			if($obj["type"] == $type) $ret[$id] = $obj["obj"];
		}
		return $ret;
	}

	/**
	* Return the content of the data in XML format
	*/	
	public function dataXML($data)
	{
		$out = "";
		foreach($data as $result)
		{
			$out .= "\t<row>\n";
			foreach ($result as $tag => $value) 
				$out .= "\t\t<".htmlspecialchars($tag).">".htmlspecialchars($value)."</".htmlspecialchars($tag).">\n";
			$out .= "\t</row>\n";
		}
		return $out;
	}

	/**
	* Return the content of the data in JSON format
	*/	
	public function dataJSON($data)
	{
		global $system;
		require_once($system->dir_real_jamp."/".$system->dir_class."/json.php");
		$json = new Services_JSON();
	 	$out = "";
		if (is_array($data))
		{
			$arrRow = array();
			foreach($data as $row)
			{
				$arrRow = array();
				foreach ($row as $k => $item) $arrRow[] = $json->encode($k).":".$json->encode($item);
				$out .= "\n{".implode(",", $arrRow)."},";
			}
			if ($out!="") $out = substr($out, 0, -1);
		}
		else $out = $json->encode($data);
		return $out;
	}
}
?>
