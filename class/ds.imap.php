<?php
/**
* Class management ds IMAP/POP/SMTP
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class imapDs extends iDS
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

		$this->property["filter"] = "";
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
	* Connects
	*/
	public function dsConnect()
	{
		if($this->property["open"] == false)
		{
			function_exists('imap_open') or ClsError::showError("IMAP000");
			$this->property["conn"] = imap_open("{".$this->property["dshost"].":".$this->property["dsport"]."}", $this->property["dsuser"], $this->property["dspwd"]) or  ClsError::showError("IMAP001");
			$this->property["open"] = true;
		} 
	}

	/**
	* Close imap connection
	*/
	public function dsClose()
	{
	    if ($this->property["open"]) imap_close($this->property["conn"]);
	}
	
	/**
	* Adds structure to the info on the selected entry
	* @param string $row line on which make parsing
	* @param array $dir directory container
	*/
 	private function addFile($dir, $list) 
 	{
 		$struc = array();
		$split = array();
		$struc['type'] = "folder";
 	    list($server, $name) = explode("}", $list);
		$last = explode(".", $name);	
		$struc[$this->property["dsparentkey"]] = $dir;
		$struc[$this->property["dskey"]] = $name;
		$struc[$this->property["dsname"]] = $last[count($last)-1];
		$index = count($this->property["result"]);
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
		$index = count($this->property["result"]);
		$this->property["result"][$index][$this->property["dsparentkey"]] = "";
		$this->property["result"][$index][$this->property["dskey"]]  = $dir;
		$this->property["result"][$index][$this->property["dsname"]] = $this->alias;
		$this->sort1[] = $dir;
		$this->sort2[] = "root";
		if (isset($struc[$this->sortname])) $this->sort3[] = $this->property["result"][$index][$this->sortname];
		else $this->sort3[] = $this->property["result"][$index][$this->property["dsname"]];
	}

	/**
	* Fetch mail 
	* @param string $dir path of the mailbox
	*/
 	public function getMail($dir) 
 	{	 
		$this->property["conn"] = imap_open("{".$this->property["dshost"].":".$this->property["dsport"]."}".$dir, $this->property["dsuser"], $this->property["dspwd"]) or  ClsError::showError("IMAP001");
		$MC = imap_check($this->property["conn"]);
		if ($MC->Nmsgs>0)
		{
			$result = imap_fetch_overview($this->property["conn"],"1:{$MC->Nmsgs}",0);
			$i=0;
			foreach ($result as $overview) 
			{
				$this->property["result"][$i]['msgno'] = $overview->msgno;
				$this->property["result"][$i]['from'] = $overview->from;
				$this->property["result"][$i]['date'] = $overview->date;
				$this->property["result"][$i]['subject'] = $overview->subject;
				$i++;
			} 
		}
	}
 	
	/**
	* Retrieve the list of mailbox
	* @param string $dir path della directory
	*/
 	private function getFiles($dir) 
 	{
		$folders = imap_list($this->property["conn"], "{".$this->property["conn"].":".$this->property["dsport"]."}", "*");
		if (is_array($folders)) 
		{
			foreach($folders  as $list) 
			{
				$index = $this->addFile($dir, $list);
// 				if ($index>-1 && $this->recursive && $this->property["result"][$index]['type'] == "folder") $this->getFiles($this->property["result"][$index][$this->property["dskey"]]);
			}
		}
 	}

	/**
	* Executes a select query
	* @param string $qry Query SQL
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
					ClsError::showError("IMAP004");
/*					$this->recursive = false;
					$this->getFile($sel, $index);*/
				 break;
				case "onelevel":
					ClsError::showError("IMAP004");
/*					$this->recursive = false;
					$this->getFiles($sel);*/
				 break;
				case "tree":	
					$this->recursive = true;
					$this->getFile($sel);
					$this->getFiles($sel);
				  break;
				default:				
					ClsError::showError("IMAP004");
				  break;
			}
 			$this->dsSort();
			if($this->property["debug"]=="true") $system->debug($this->property["id"], "Selected filesystem: ".$sel);
			$index++;
		} 
	}

    public function create_mailbox($mailbox) 
	{
		imap_createmailbox($this->property["conn"], imap_utf7_encode("{".$this->property["conn"].":".$this->property["dsport"]."}$mailbox")) or ClsError::showError("IMAP004");
	}

    public function delete_mailbox($mailbox) 
	{
		imap_deletemailbox($this->property["conn"], imap_utf7_encode("{".$this->property["conn"].":".$this->property["dsport"]."}$mailbox")) or ClsError::showError("IMAP003");
	}

	public function dsQueryUpdate($qry = null)
	{
	}

	public function dsQueryDeleteAll($qry = null)
	{
	}
		
	public function dsQueryDelete($qry = null)
	{
	}
	
	public function dsQueryInsert($qry = null)
	{
	}

 	public function dsSort() 
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
	
	public function dsQueryRefresh()
	{
  	}

	public function dsImport($result, $method)
	{
  	}
}
?>
