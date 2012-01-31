<?php
/**
* Class management ds FTP
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ftpDs extends iDS
{	
	public  $systype;

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

		$this->property["dskey"] = "path";
		$this->property["dsparentkey"] = "dirpath";
		$this->property["dsname"] = "filename";

		$this->property["dsorder"] = "";
		$this->property["scope"] = "";	
		$this->property["justthese"] = null; // SYSTYPE=UNIX [perms,number,owner,group,size,datetime,md5]
											 			 // SYSTYPE=WINDOWS_NT [size,datetime]
		$this->property["start"] = 0;
		$this->property["limit"] = 0;
		$this->property["debug"] = false;

		$this->property["filter"]  = "";	// "", "nofiles", "nodirectory", "html|pdf"
		$this->property["dsport"] = 21;
		$this->property["poscount"] = 0;
		$this->property["open"]  = false;

		$this->property["qrycount"] = 0;
	}

	/**
	* Destruct
	*/
	public function __destruct()
	{
	  $this->dsClose();
	}
	
	/**
	* Connects to the ftp
	*/
	public function dsConnect()
	{
		if($this->property["open"] == false)
		{
			function_exists('ftp_connect') or ClsError::showError("FTP000");
			$this->property["conn"] = ftp_connect($this->property["dshost"], (integer)$this->property["dsport"]) or ClsError::showError("FTP001", $this->property["dshost"]);
			if (isset($this->property["dsuser"])) 
			{
				$this->property["open"] = ftp_login($this->property["conn"], $this->property["dsuser"], $this->property["dspwd"]) or ClsError::showError("FTP002");
			}
			$this->systype = ftp_systype($this->property["conn"]) or ClsError::showError("FTP001", $this->property["dshost"]);
			$this->property["open"] = true;
		} 
	}

	/**
	* Close ftp connection
	*/
	public function dsClose()
	{
	    if ($this->property["open"]) ftp_close($this->property["conn"]);
	}

	/**
	* Adds structure to the info on the selected entry
	* @param string $row line on which make parsing
	* @param array $dir directory container
	*/
 	private function addFile($row, $dir, $filter) 
 	{
		$struc = array();
		$split = array();
		$struc['type'] = "file";
		$index = count($this->property["result"]);
		switch ($this->systype)
		{
			case "Windows_NT":
				preg_match("/([0-9]{2})-([0-9]{2})-([0-9]{2}) +([0-9]{2}):([0-9]{2})(AM|PM) +([0-9]+|<DIR>) +(.+)/", $row, $split);
				if ($split[8]=="" || $split[8]=="." || $split[8]=="..") return -1;
				if ($split[7]=="<DIR>") $struc['type'] = "folder";
                if ($filter=="nodirectory" || $filter=="nofiles")
                {
                    if ($struc['type']=="folder" && $filter=="nodirectory") return -1; 
                    else if ($struc['type']!="folder" && $filter=="nofiles") return -1; 
                }
        		else if ($filter!="" && !preg_match("/\.(".$filter.")/i", $split[8])) return -1;

				$this->property["qrycount"]++;
				if ($this->property["qrycount"]<$this->property["start"]+1) return -1;
				if (($this->property["limit"]>0) && ($index>$this->property["limit"]-1)) return -1;
				if (isset($this->justthese)) 
				{
					if (isset($this->justthese["size"])) $struct['size'] = $split[7];
					if (isset($this->justthese["datetime"])) 
					{
						if ($split[3]<70) $split[3]+=2000; 
						else $split[3]+=1900;
						$struct['datetime'] = $split[1]."-".$split[2]." ".$split[3];
					}
				}
				else
				{
					$struct['size'] = $split[7];
					if ($split[3]<70) $split[3]+=2000; 
					else $split[3]+=1900;
					$struct['datetime'] = $split[1]."-".$split[2]." ".$split[3];
				}
			break;
			case "UNIX":
				$split = preg_split("[ ]", $row, 9, PREG_SPLIT_NO_EMPTY);
				if ($split[8]=="" || $split[8]=="." || $split[8]=="..") return -1;
		 		if ($split[0]{0} === "d") $struc['type'] = "folder";
		 		else if ($split[0]{0} === "l")
		 		{
					$struc['type'] = "filelink";
		 			if (substr($split[8], -1)=="/") $struc['type'] = "folderlink";
	 			}
				if ($filter=="nodirectory" || $filter=="nofiles")
				{
					 if ($struc['type']=="folder" && $filter=="nodirectory") return -1; 
					 else if ($struc['type']!="folder" && $filter=="nofiles") return -1; 
				}
				else if ($filter!="" && !preg_match("/(".$filter.")/i", $split[8])) return -1;

				$this->property["qrycount"]++;
				if ($this->property["qrycount"]<$this->property["start"]+1) return -1;
				if (($this->property["limit"]>0) && ($index>$this->property["limit"]-1)) return -1;
				if (isset($this->justthese)) 
				{
					if (isset($this->justthese["perms"]))	$struc['perms']  = $split[0];
					if (isset($this->justthese["number"]))	$struc['number'] = $split[1];
					if (isset($this->justthese["owner"]))	$struc['owner']  = $split[2];
					if (isset($this->justthese["group"]))	$struc['group']  = $split[3];
					if (isset($this->justthese["size"]))	$struc['size']   = $split[4];
					if (isset($this->justthese["datetime"])) $struc['date'] = date("m-d H:i", strtotime($split[5]."-".$split[6]." ".$split[7]));
					if (isset($this->justthese["md5"]) && $struc['type'] == "file") 
					{
						$md5 = ftp_raw($this->property["conn"], "md5sum \"$dir/$split[8]\"");
						$struc['md5'] = $md5[0];
					}			
	 			}
				else 
				{
					$struc['perms']  = $split[0];
					$struc['number'] = $split[1];
					$struc['owner']  = $split[2];
					$struc['group']  = $split[3];
					$struc['size']   = $split[4];
					$struc['date'] = date("m-d H:i", strtotime($split[5]."-".$split[6]." ".$split[7]));
					if ($struc['type'] == "file") 
					{
						$md5 = ftp_raw($this->property["conn"], "md5sum \"$dir/$split[8]\"");
						$struc['md5'] = $md5[0];
					}			
	 			}
			   break;	
			default:
				ClsError::showError("FTP007");
			break;
		}
		$struc[$this->property["dsparentkey"]] = $dir;
		$struc[$this->property["dskey"]] = "$dir/$split[8]";
		$struc[$this->property["dsname"]] = $split[8];
		$this->property["result"][$index] = $struc;
		$this->sort1[] = $dir;
		$this->sort2[] = $struc['type'];
		if (isset($struc[$this->sortname])) $this->sort3[] = $struc[$this->sortname];
		else $this->sort3[] = $struc[$this->property["dsname"]];
		return $index;
	}

	/**
	* Info Recover the root node
	* @param string $dir path of the directory
	*/
 	private function getFile($dir) 
 	{		
		@ftp_chdir($this->property["conn"], $dir);
 		$list = @ftp_rawlist($this->property["conn"], "-a ", false);
		$list[0] = substr($list[0], 0, -1).$this->alias;
		$index = $this->addFile($list[0], "", ""); 
		$this->property["result"][$index][$this->property["dsparentkey"]] = "";
		$this->property["result"][$index][$this->property["dskey"]]  = $dir;
		return $index;
	}

	/**
	* Retrieve the list of specified files
	* @param string $dir path of the directory
	*/
 	private function getFiles($dir) 
 	{		
		@ftp_chdir($this->property["conn"], $dir);
 		$list = @ftp_rawlist($this->property["conn"], "-a ", false);
 		for ($i=0; $i<count($list); $i++) 
		{
			$index = $this->addFile($list[$i], $dir, $this->filter);
 			if ($index>-1 && $this->recursive && $this->property["result"][$index]['type']=="folder") $this->getFiles($this->property["result"][$index][$this->property["dskey"]]);
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
					$idx = $this->getFile($sel); 
					$this->property["result"][$idx][$this->property["dsname"]] = $this->alias;
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
					ClsError::showError("FTP004");
				  break;
			}
 			$this->dsSort();
			if($this->property["debug"]=="true") $system->debug($this->property["id"], "Selected filesystem: ".$sel);
			$index++;
		}
	}

	/**
	* Executes a update query
	* @param string $qry Query
	*/
	public function dsQueryUpdate($qry = null)
	{
		global $system;
		$path = $_POST[$_POST['keyname']];
		$explode = explode("/", $path);
		$name = $explode[0];
		if (isset($_POST[$this->property["dsname"]])) $name = $_POST[$this->property["dsname"]];
		if (isset($_POST[$this->property["dsparentkey"]]))	$dirpath = $_POST[$this->property["dsparentkey"]];
		else 
 		{
			unset($explode[0]);
			$dirpath = implode("/", $path);
		}
		$newpath = "$dirpath/$name";
		if ($path != $newpath) 
		{
			ftp_rename($this->property["conn"], $path, $newpath) or ClsError::showError("FTP002");
			if($this->property["debug"]=="true") $system->debug($this->property["id"], "Updated filesystem: from ".$path." to ".$newpath);
		}
		unset($this->property["select"]);
	}

	/**
	* Executes a truncate query
	* @param string $qry Query
	*/
	public function dsQueryDeleteAll($qry = null)
	{
	}

	/**
	* Recursive function cancellation
	* @param string $dir file or directory to be deleted
	*/
	private function RecursiveDelete($dir)
	{
		if (!(@ftp_rmdir($this->property["conn"], $dir) || @ftp_delete($this->property["conn"], $dir)))
		{
			ftp_chdir($this->property["conn"], $dir) or ClsError::showError("FTP005");
			$list = ftp_nlist($this->property["conn"], "-a ");
 			for ($i=0; $i<count($list); $i++) 
  			{			
  			if ($list[$i]!="." && $list[$i]!=".." && $list[$i]!="") $this->RecursiveDelete("$dir/$list[$i]");
  			}
 			ftp_rmdir($this->property["conn"], $dir) or ClsError::showError("FTP005");
 			if($this->property["debug"]=="true") $system->debug($this->property["id"], "Deleted: ".$dir);
		}
	}
		
	/**
	* Executes a delete query
	* @param string $qry Query
	*/
	public function dsQueryDelete($qry = null)
	{
 		if (isset($this->property["select"])) $this->RecursiveDelete($this->property["select"]);
		unset($this->property["select"]);
	}
	
	/**
	* Executes a insert query
	* @param string $qry Query
	*/
	public function dsQueryInsert($qry = null)
	{
		if (!isset($_POST['path']) || !isset($_POST['filename'])) return; 
		$dir = $_POST['path']."/".$_POST['filename'];
		ftp_mkdir($this->property["conn"], $dir) or ClsError::showError("FTP006");
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
