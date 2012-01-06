<?php
/**
* Class management ds XML
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/
class xmlDs extends iDS
{	
	/**
	* Construct
	*/
	public function __construct()
	{
		$this->sort1 = array();
		$this->sort2 = array();
		$this->sort3 = array();
		$this->recursive = false;
		$this->sortname = "name";
		$this->order = "ASC";

		$this->property["dskey"] = "key";
		$this->property["dsparentkey"] = "parentkey";
		$this->property["dsname"] = "name";
		$this->property["dsorder"] = "";

		$this->property["scope"] = "";	
		$this->property["justthese"] = null;

		$this->property["start"] = 0;
		$this->property["limit"] = 0;
		$this->property["debug"] = false;

		$this->property["filter"]  = "";
		$this->property["dsport"] = 22;
		$this->property["poscount"] = 0;
		$this->property["open"]  = false;

		$this->property["qrycount"] = 0;
	}

	/**
	* Connects to the XML
	*/
	public function dsConnect()
	{
		if($this->property["open"] == false)
		{
			global $system;
			if (strpos($this->property["dshost"], "://")===false)
			      $this->property["dshost"] = realpath($this->property["dshost"]);
			try 
			{
				$this->property["xml"] = new DOMDocument();
				$this->property["xml"]->preserveWhiteSpace = false;
				if (empty($this->property["dsdefault"])) $this->property["xml"]->load($this->property["dshost"]);
				else $this->property["xml"]->loadXML($this->property["dsdefault"]);
				$this->property["xpath"] = new DOMXPath($this->property["xml"]);
				$this->property["open"] = true;
			}  
			catch (Exception $e) 
			{
				ClsError::showError("XML001", $this->property["dshost"]);
			} 
		}
	}

	/**
	* Returns info on the Node
	* @param  string $nodePath path of the node
	* @return array info on node
	*/
	private function getInfoNode($nodePath) 
	{
		$node["path"] = $nodePath;
		$explode = explode("/", $node["path"]);
		$node["name"] = $explode[count($explode)-1];
		unset($explode[count($explode)-1]);
		$node["parentpath"] = implode("/", $explode);
		return $node;
	}

	/**
	* Read XML node attributes
	* @param string $parentPath full path of the node
	* @param DOMNode $node XML node
	*/
	private function getAttributes($parentPath, $node) 
	{	
		$this->property["qrycount"]++;
		$index = count($this->property["result"]);
		if ($this->property["qrycount"]<$this->property["start"]+1) return -1;
		if (($this->property["limit"]>0) && ($index>$this->property["limit"]-1)) return -1;
		$this->property["result"][$index][$this->property["dsparentkey"]] = $parentPath;	
		$this->property["result"][$index][$this->property["dskey"]] = $parentPath."/".$node->nodeName;	
		$this->property["result"][$index][$this->property["dsname"]] = $node->nodeName;
		if($node->hasAttributes())
		{
			$attributes = $node->attributes;
			if (isset($this->justthese)) 
			{
				foreach($attributes as $attribute) 
				{
					if (isset($this->justthese[$attribute->name])) $this->property["result"][$index][$attribute->name] = (string)$attribute->value;
				}
			}
			else foreach($attributes as $attribute) $this->property["result"][$index][$attribute->name] = (string)$attribute->value;
		}
		$this->property["result"][$index]["type"] = "file";
		if ($node->hasChildNodes()) $this->property["result"][$index]["type"] = "folder";
 		$this->sort1[] = $parentPath;
 		$this->sort2[] = $this->property["result"][$index]["type"];
		if (isset($this->property["result"][$index][$this->sortname])) $this->sort3[] = $this->property["result"][$index][$this->sortname];
		else $this->sort3[] = $this->property["result"][$index][$this->property["dsname"]];
		return $index;
	}

	/**
	* Info Recover the root node
	* @param string $nodePath full path of the node
	*/
 	private function getFile($nodePath) 
 	{
		$nodeobj = $this->getInfoNode($nodePath);
		$node = $this->property["xpath"]->query($nodePath)->item(0);
 		$index = $this->getAttributes("", $node);
		$this->property["result"][$index][$this->property["dsparentkey"]] = "";
		$this->property["result"][$index][$this->property["dskey"]] = $nodeobj["path"];
		$this->property["result"][$index][$this->property["dsname"]] = $this->alias;
	}

	/**
	* Retrieve the list of specified files
	* @param string $parentPath full path of the node
	*/
 	private function getFiles($parentPath) 
 	{		
		$nodes = $this->property["xpath"]->query("$parentPath/*");
		foreach($nodes as $node) 
		{
			$index = $this->getAttributes($parentPath, $node);
			if ($index>-1 && $this->recursive) $this->getFiles($this->property["result"][$index][$this->property["dskey"]]);
		}
 	}

	/**
	* Executes a select query
	* @param string $qry Query
	*/
	public function dsQuerySelect($qry = null)
	{
		global $system;
		$index=0;
		$this->property["poscount"] = 0;
 		if ($this->property["dsorder"]!="") list($this->sortname, $this->order) = explode(" ", $this->property["dsorder"]);
		if(isset($_POST["scope"])) $this->property["scope"] = $_POST["scope"];
		if (!isset($this->property["select"])) 	$this->property["select"] = $this->property["base"];
		if (!is_array($this->property["select"])) $this->property["select"] = array($this->property["select"]);
 		if (!isset($this->property["alias"])) $this->property["alias"] = $this->property["select"];
		foreach ($this->property["select"] as $sel) 
		{
			$this->alias = $this->getProperty("alias", $index);
			$this->scope = $this->getProperty("scope", $index);
			$this->filter = $this->getProperty("filter", $index);
			$this->justthese = $this->getProperty("justthese", $index);
			if (isset($this->justthese)) $this->justthese = array_flip(explode(",", $this->justthese));
			switch ($this->scope) 
			{
				case "base":			
					$this->recursive = false;
					$this->getFile($sel); 
				 break;
				case "onelevel":
					$this->recursive = false;
					$this->getFiles($sel);
				 break;
				case "tree":	
					$this->recursive = true;
					$this->getFile($sel);
					$this->getFiles($sel);
				  break;
				default:				
					ClsError::showError("XML004");
				  break;
			}
 			if ($this->property["dsorder"]!="") $this->dsSort();
			if($this->property["debug"]=="true") $system->debug($this->property["id"], "Selected Node: ".$sel);
			$index++;
		} 
	}

	/**
	* Create a new DOMNode $ NewNode $ name calling and
	* setting attributes equal to $ node
	* @param DOMNode $node node colonies
	* @param string  $name name of the new cloned node
	* @return DOMNode $newnode node cloned
	*/
	private function renameNode($node, $name)
	{
		$newnode = $this->property["xml"]->createElement($name);	
		if($node->hasAttributes())
		{
			$attributes = $node->attributes;
			foreach($attributes as $attribute) $newnode->setAttribute($attribute->name, (string)$attribute->value);
		}
		if($node->hasChildNodes())
		{
			$childen = $node->childNodes;
			foreach($childen as $child) $newnode->appendChild($child);
		}
		$node->parentNode->appendChild($newnode);
		$node->parentNode->removeChild($node);
		return $newnode;
	}

	/**
	* Executes a update query
	* @param string $qry Query
	*/
	public function dsQueryUpdate($qry = null)
	{
		global $system;
		$nodeobj = $this->getInfoNode($_POST['keynamevalue']);

		$node = $this->property["xpath"]->query($nodeobj["path"])->item(0) or ClsError::showError("XML003");
		// Node renamed
 		if (isset($_POST[$this->property["dsname"]])) 
 		{
			$newname = $_POST[$this->property["dsname"]];
			if ($nodeobj["name"] != $newname) $node = $this->renameNode($node, $newname);
 		}

		// Node moved
 		if (isset($_POST[$this->property["dsparentkey"]])) 
 		{
			$parentkey = $_POST[$this->property["dsparentkey"]];		
 			if ($nodeobj["parentpath"] != $parentkey) 
 			{
 				$parent = $this->property["xpath"]->query($parentkey)->item(0) or ClsError::showError("XML003");
 				$parent->appendChild($node);
 			}
 		}

		// Edit Attributes
 		$flag = false;
 		foreach ($_POST as $key => $value)
 		{
 			if ($key=="name") $flag=true;
 			else if ($flag && $key!="returnxml") $node->setAttribute($key, $value);
 		}		
		$this->property["xml"]->save($this->property["dshost"]);
		if($this->property["debug"]=="true") $system->debug($this->property["id"], "Updated node: ".$this->property["select"]);
		unset($this->property["select"]);
	}

	/**
	* Recursive function for deleting a node XML
	*/			
	private function NodeDelete($parentNode, $nodeXML)
	{
		$nodes = $nodeXML->childNodes;
		foreach($nodes as $node) $this->NodeDelete($nodeXML, $node);
		$parentNode->removeChild($nodeXML);
	}

	/**
	* Executes a truncate query
	* @param string $qry Query
	*/
	public function dsQueryDeleteAll($qry = null)
	{
	}
		
	/**
	* Executes a delete query
	* @param string $qry Query
	*/
	public function dsQueryDelete($selectdn = null)
	{
		global $system;
		if (isset($this->property["select"])) 
		{
			$split = explode("/", $this->property["select"]);
			unset($split[count($split)-1]);
			$parentName = implode("/", $split);
			$parentNode = $this->property["xpath"]->query($parentName)->item(0);
			$node = $this->property["xpath"]->query($this->property["select"])->item(0);
			$this->NodeDelete($parentNode, $node);
			$this->property["xml"]->save($this->property["dshost"]);
			if($this->property["debug"]=="true") $system->debug($this->property["id"], "Deleted node: ".$this->property["select"]);
			unset($this->property["select"]);
		}
	}

	/**
	* Executes a insert query
	* @param string $qry Query
	*/
	public function dsQueryInsert($qry = null)
	{
		global $system;
		$parentkey = $_POST[$this->property["dsparentkey"]];		
		$parent = $this->property["xpath"]->query($parentkey)->item(0) or ClsError::showError("XML003");
		$node = $this->property["xml"]->createElement("newnode");
		$parent->appendChild($node);
		$flag = false;
		foreach ($_POST as $key => $value)
		{
			if ($key=="name") $flag=true;
			else if ($flag && $key!="returnxml") $node->setAttribute($key, $value);
		}		
		$this->property["xml"]->save($this->property["dshost"]);
		if($this->property["debug"]=="true") $system->debug($this->property["id"], "Insert node: ".$this->property["select"]);
		unset($this->property["select"]);
	}

	/**
	* Move the pointer of the results
	* @param string $row number line
	*/
	public function dsMoveRow($row)
	{
		$this->property["poscount"] = $row;
	}

	/**
	* 	Run the last query executed
	*/
	public function dsQueryRefresh()
	{
	}

	/**
	* Import data from another result
	* @param result $result Result to be imported
	* @param string $method Import method
	*/
	public function dsImport($result, $method)
	{
  	}
}
?>
