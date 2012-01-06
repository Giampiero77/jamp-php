<?php
/**
* Class management ds FILESYSTEM
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/
class filesystemDs extends iDS
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

		$this->property["dskey"] = "path";
		$this->property["dsparentkey"] = "dirpath";
		$this->property["dsname"] = "filename";
		$this->property["dsorder"] = "";
		$this->property["scope"] = "";	
		$this->property["justthese"] = null; // dev,ino,mode,nlink,uid,gid,rdev,size,atime,mtime,ctime,blksize,blocks,md5
		$this->property["start"] = 0;
		$this->property["limit"] = 0;
		$this->property["debug"] = false;

		$this->property["filter"] = "";	// "", "nofiles", "nodirectory", "html|pdf"
		$this->property["poscount"] = 0;
		$this->property["open"]  = false;

		$this->property["qrycount"] = 0;
	}
	
	/**
	* Connects to the filesystem
	*/
	public function dsConnect()
	{
		if ($this->property["open"] == false) 
		{
			global $system;
 			if (file_exists($this->property["base"])) $this->property["open"] = true;
 			else ClsError::showError("FILE001", $this->property["base"]);
		}
	}

	/**
	* Adds structure to the info on the selected entry
	* @param string $dir parent directory path
	* @param string $filepath full path of entry
	* @param string $filename filename
	*/
 	private function addFile($dir, $filepath, $filename, $filter) 
 	{
		$struc = array();
		$split = array();
		$struc['type'] = "file";
 		if (is_dir($filepath))  $struc['type'] = "folder";
 		if (is_link($filepath)) $struc['type'] .= "link";

		$index = count($this->property["result"]);

        if ($filter=="nodirectory" || $filter=="nofiles")
        {
            if ($struc['type']=="folder" && $filter=="nodirectory") return -1; 
            else if ($struc['type']!="folder" && $filter=="nofiles") return -1; 
        }
		else if ($filter!="" && !preg_match("/(".$filter.")/i", $filename)) return -1;

		$this->property["qrycount"]++;
		$index = count($this->property["result"]);
		if ($this->property["qrycount"]<$this->property["start"]+1) return -1;
		if (($this->property["limit"]>0) && ($index>$this->property["limit"]-1)) return -1;

		$split = stat($filepath);
		if (isset($this->justthese)) 
		{
			if (isset($this->justthese["dev"]))	 $struc['dev']  = $split[0];			// 0 device number
			if (isset($this->justthese["ino"]))	 $struc['ino'] = $split[1];  			// 1 inode number *
			if (isset($this->justthese["mode"])) $struc['mode'] = $split[2];  		// 2 inode protection mode
			if (isset($this->justthese["nlink"])) $struc['nlink'] = $split[3];  		// 3 number of links
			if (isset($this->justthese["uid"])) $struc['uid'] = $split[4];  			// 4 userid of owner *
			if (isset($this->justthese["gid"])) $struc['gid'] = $split[5];  			// 5 groupid of owner *
			if (isset($this->justthese["rdev"])) $struc['rdev'] = $split[6];  		// 6 device type, if inode device
			if (isset($this->justthese["size"])) $struc['size'] = $split[7]; 			// 7 size in bytes
			if (isset($this->justthese["atime"])) $struc['atime'] = $split[8];  		// 8 time of last access (Unix timestamp)
			if (isset($this->justthese["mtime"])) $struc['mtime'] = $split[9];  		// 9 time of last modification (Unix timestamp)
			if (isset($this->justthese["ctime"])) $struc['ctime'] = $split[10];  	// 10 time of last inode change (Unix timestamp)
			if (isset($this->justthese["blksize"])) $struc['blksize'] = $split[11];	// 11 blocksize of filesystem IO **
			if (isset($this->justthese["blocks"])) $struc['blocks'] = $split[12];  	// 12 number of blocks allocated **
			if (isset($this->justthese["md5"]) && $struc['type'] == "file") $struc['md5'] = md5_file($filepath);
		}
		else
		{
			$struc['dev']  = $split[0];		// 0 device number
			$struc['ino'] = $split[1];  		// 1 inode number *
			$struc['mode'] = $split[2];  		// 2 inode protection mode
			$struc['nlink'] = $split[3];  	// 3 number of links
			$struc['uid'] = $split[4];  		// 4 userid of owner *
			$struc['gid'] = $split[5];  		// 5 groupid of owner *
			$struc['rdev'] = $split[6];  		// 6 device type, if inode device
			$struc['size'] = $split[7]; 		// 7 size in bytes
			$struc['atime'] = $split[8];  	// 8 time of last access (Unix timestamp)
			$struc['mtime'] = $split[9];  	// 9 time of last modification (Unix timestamp)
			$struc['ctime'] = $split[10]; 	// 10 time of last inode change (Unix timestamp)
			$struc['blksize'] = $split[11];	// 11 blocksize of filesystem IO **
			$struc['blocks'] = $split[12];  	// 12 number of blocks allocated **
			if ($struc['type'] == "file") $struc['md5'] = md5_file($filepath);
		}
		// * On Windows this will always be 0.
		// ** Only valid on systems supporting the st_blksize type - other systems (e.g. Windows) return -1. 
		$struc[$this->property["dsparentkey"]] = "$dir";
		$struc[$this->property["dskey"]] = "$filepath";
		$struc[$this->property["dsname"]] = "$filename";
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
 		$index = $this->addFile("", $dir, $this->alias, "");
		$this->property["result"][$index][$this->property["dsparentkey"]] = "";
		$this->property["result"][$index][$this->property["dskey"]] = $dir;
 	}

	/**
	* Retrieve the list of specified files
	* @param string $dir path of the directory
	*/
 	private function getFiles($dir) 
 	{		
		$d = dir($dir);
		if (!$d) ClsError::showError("FILE001");
		while (false !== ($list = $d->read())) 
		{
			if ($list=="" || $list=="." || $list=="..") continue;
			$index = $this->addFile($dir, "$dir/$list", $list, $this->filter);
 			if ($index>-1 && $this->recursive && $this->property["result"][$index]['type'] == "folder") $this->getFiles($this->property["result"][$index][$this->property["dskey"]]);
		}
		$d->close();
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
					ClsError::showError("FILE004");
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
		if (!isset($_POST[$_POST['keyname']])) return; 
		$path = $_POST[$_POST['keyname']];
		$explode = explode("/", $path);
		$name = $explode[0];
		if (isset($_POST[$this->property["dsname"]])) $name = $_POST[$this->property["dsname"]];
		if (isset($_POST[$this->property["dsparentkey"]])) $dirpath = $_POST[$this->property["dsparentkey"]];
		else 
 		{
			unset($explode[0]);
			$dirpath = implode("/", $path);
		}
		$newpath = "$dirpath/$name";
		if ($path != $newpath) 
		{
			rename($path, $newpath) or ClsError::showError("FILE002");
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
	* Executes a delete query
	* @param string $qry Query
	*/
	public function dsQueryDelete($qry = null)
	{
 		if (isset($this->property["select"])) 
		{
			unlink($this->property["select"]) or ClsError::showError("FILE005");
			if($this->property["debug"]=="true") $system->debug($this->property["id"], "Deleted file: ".$this->property["select"]);
		}
		unset($this->property["select"]);
	}
	
	/**
	* Executes a insert query
	* @param string $qry Query
	*/
	public function dsQueryInsert($qry = null)
	{
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
