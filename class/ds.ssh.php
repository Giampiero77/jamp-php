<?php
/**
* Class management ds SSH
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class sshDs extends iDS
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
		$this->sortname = "filename";
		$this->order = "ASC";

		$this->property["dskey"] = "path";
		$this->property["dsparentkey"] = "dirpath";
		$this->property["dsname"] = "filename";
		$this->property["dsorder"] = "";
		$this->property["scope"] = "";	
		$this->property["justthese"] = null; // [perms,number,owner,group,size,datetime,md5]
		$this->property["start"] = 0;
		$this->property["limit"] = 0;
		$this->property["debug"] = false;

		$this->property["filter"]  = "";	// "", "nofiles", "nodirectory", "html|pdf"
		$this->property["dsport"] = 22;
		$this->property["poscount"] = 0;
		$this->property["open"]  = false;

		$this->property["pubkeyfile"] = null;
		$this->property["privkeyfile"] = null;

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
	* Connects to the SSH
	*/
	public function dsConnect()
	{
		if($this->property["open"] == false)
		{
			function_exists('ssh2_connect') or ClsError::showError("SSH000");
			if (isset($this->property["pubkeyfile"]) && isset($this->property["privkeyfile"]))
			{
				$this->property["conn"] = ssh2_connect($this->property["dshost"], (integer)$this->property["dsport"], array('hostkey'=>'ssh-rsa')) or  ClsError::showError("SSH001");
				ssh2_auth_pubkey_file($this->property["conn"], $this->property["dsuser"], $this->property["pubkeyfile"], $this->property["privkeyfile"],$this->property["dspwd"]) or ClsError::showError("SSH002");
			}
			else 
			{
				$this->property["conn"] = ssh2_connect($this->property["dshost"], (integer)$this->property["dsport"]) or  ClsError::showError("SSH001");
				if (isset($this->property["dsuser"])) 
				{
					if ($this->property["dspwd"]!="none") 
						ssh2_auth_password($this->property["conn"],$this->property["dsuser"], $this->property["dspwd"]) or ClsError::showError("SSH002");
					else ssh2_auth_none($this->property["conn"], $this->property["dsuser"]) or ClsError::showError("SSH002");
				}
			}
			$this->property["open"] = true;
		} 
	}

	/**
	* Executes a command remotely, SSH Protocol
	* @param string $cmd command to run
	*/
 	private function exec($cmd) 
 	{		
		$stream = ssh2_exec($this->property["conn"], $cmd);
 		if (!$stream) ClsError::showError("SSH003");
		stream_set_blocking($stream, true);
		return $stream;
	}

	/**
	* Copy a file from the remote server to the local filesystem using the SCP protocol. 
	* @param string $remotefile remote file
	* @param string $localfile local file
	*/
	public function scp_recv($remotefile, $localfile)
	{
		$remotefile = str_replace(" ", "\ ", $remotefile);
		ssh2_scp_recv($this->property["conn"], $remotefile, $localfile);
	}

	/**
	* Deletes a file on the remote filesystem. 
	* @param string $remotefile remote file
	*/
	public function unlink($remotefile)
	{
		$remotefile = str_replace(" ", "\ ", $remotefile);
		ssh2_sftp_unlink($this->property["conn"], $remotefile);
	}

	/**
	* Adds structure to the info on the selected entry
	* @param string $row riga which make parsing
	* @param array $dir directory container
	*/
 	private function addFile($row, $dir, $filter) 
 	{
		$struc = array();
		$split = array();
		$struc['type'] = "file";
		$split = preg_split("[ ]", $row, 8, PREG_SPLIT_NO_EMPTY);
		if ($split[0]{0} === "d") $struc['type'] = "folder";                                  
		else if ($split[0]{0} === "l")
		{
			$struc['type'] = "filelink";
			if (substr($split[7], -1)=="/") $struc['type'] = "folderlink";
		}

        if ($filter=="nodirectory" || $filter=="nofiles")
        {
            if ($struc['type']=="folder" && $filter=="nodirectory") return -1; 
            else if ($struc['type']!="folder" && $filter=="nofiles") return -1; 
        }
		else if ($filter!="" && !preg_match("/(".$filter.")/i", $split[7])) return -1;

		$this->property["qrycount"]++;
		$index = count($this->property["result"]);
		if ($this->property["qrycount"]<$this->property["start"]+1) return -1;
		if (($this->property["limit"]>0) && ($index>$this->property["limit"]-1)) return -1;   
		if (isset($this->justthese)) 
		{
			if (isset($this->justthese["perms"]))	$struc['perms']  = $split[0];
			if (isset($this->justthese["number"]))	$struc['number'] = $split[1];
			if (isset($this->justthese["owner"]))	$struc['owner']  = $split[2];
			if (isset($this->justthese["group"]))	$struc['group']  = $split[3];
			if (isset($this->justthese["size"]))	$struc['size']   = $split[4];
			if (isset($this->justthese["datetime"])) $struc['date'] = $split[5]." ".$split[6];
			if (isset($this->justthese["md5"]) && $struc['type'] == "file") 
			{
				$stream = $this->exec("md5sum \"$dir/$split[7]\"");
				list($struc['md5']) = explode(" ", fgets($stream));
			}			
		}			
		else 
		{
			$struc['perms']  = $split[0];
			$struc['number'] = $split[1];
			$struc['owner']  = $split[2];
			$struc['group']  = $split[3];
			$struc['size']   = $split[4];
			$struc['date'] = $split[5]." ".$split[6];
			if ($struc['type'] == "file") 
			{
				$stream = $this->exec("md5sum \"$dir/$split[7]\"");
				list($struc['md5']) = explode(" ", fgets($stream));
			}			
		}			
		$struc[$this->property["dsparentkey"]] = $dir;
		$struc[$this->property["dskey"]] = "$dir/$split[7]";
		$struc[$this->property["dsname"]] = $split[7];
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
 	    $filter = $this->filter;  
		$stream = $this->exec("ls -ld \"$dir\"");
		$list = fgets($stream);
 		$index = $this->addFile($list, "", "");
		$this->property["result"][$index][$this->property["dsparentkey"]] = "";
		$this->property["result"][$index][$this->property["dskey"]]  = $dir;
		$this->property["result"][$index][$this->property["dsname"]] = $this->alias;
 	    $this->filter = $filter;  
	}

	/**
	* Retrieve the list of specified files
	* @param string $dir path of the directory
	*/
 	private function getFiles($dir) 
 	{		
		$stream = $this->exec("ls -l \"$dir\" | grep -v ^total");
		while($list = fgets($stream)) 
		{
			flush();
			$index = $this->addFile(trim($list), $dir, $this->filter);
 			if ($index>-1 && $this->recursive && $this->property["result"][$index]['type'] == "folder") $this->getFiles($this->property["result"][$index][$this->property["dskey"]]);
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
					ClsError::showError("SSH004");
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
		$path = $_POST[$_POST['keyname']];
		$explode = explode("/", $path);
		$name = $explode[0];
		if (isset($_POST[$this->property["dsname"]])) 
 		{
			$name = $_POST[$this->property["dsname"]];
		}
		if (isset($_POST[$this->property["dsparentkey"]])) 
 		{
			$dirpath = $_POST[$this->property["dsparentkey"]];
		}
		else 
 		{
			unset($explode[0]);
			$dirpath = implode("/", $path);
		}
		$newpath = $dirpath."/".$name;
		if ($path != $newpath) 
 		{
			$this->exec("mv '".$path."' '".$newpath."'") or ClsError::showError("FILE002");
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
			$this->exec("rm -r '".$this->property["select"]."'");
			if($this->property["debug"]=="true") $system->debug($this->property["id"], "Deleted filesystem: ".$this->property["select"]);
		}
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
