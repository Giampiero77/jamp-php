<?php
/**
* Class management ds LDAP
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ldapDs extends iDS
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
		$this->sortname = "dn";
		$this->order = "ASC";

		$this->property["dskey"]   	= "dn";
		$this->property["dsparentkey"]	= "parentdn";
		$this->property["dsname"]   	= "entry";
		$this->property["dsorder"]	= "";
		$this->property["scope"]   	= "base";
		$this->property["filter"]   	= "(objectClass=*)";
		$this->property["justthese"]   	= null;
		$this->property["start"]   	= 0;
		$this->property["limit"] 	= 0;
		$this->property["debug"] 	= false;

		$this->property["dsport"] 	 = 389;
		// tree(search over the tree from the specified dn), onelevel(within the branch), base(only the specified entry)
		$this->property["poscount"] 	= 0;
		$this->property["open"]  	= false;
		$this->property["fetch"] 	= "object";
	}

	/**
	* Destruct
	*/
	public function __destruct()
	{
	      $this->dsClose();
	}

	/**
	* Connects to the LDAP
	*/
	public function dsConnect()
	{
		if($this->property["open"] == false)
		{
			function_exists('ldap_connect') or ClsError::showError("LDAP000");
 			$this->property["conn"] = ldap_connect($this->property["dshost"], (integer)$this->property["dsport"]) or ClsError::showError("LDAP001");
 			ldap_set_option($this->property["conn"], LDAP_OPT_PROTOCOL_VERSION, 3) or ClsError::showError("LDAP002");
 			if (isset($this->property["dsuser"]) && isset($this->property["dspwd"])) ldap_bind($this->property["conn"], $this->property["dsuser"], $this->property["dspwd"]) or ClsError::showError("LDAP003");
			else ldap_bind($this->property["conn"]) or ClsError::showError("LDAP003");
			$this->property["open"] = true;
		}
	}

	/**
	* Close imap connection
	*/
	public function dsClose()
	{
	  if ($this->property["open"]) @ldap_close($this->property["conn"]);
	}

	/**
	* Login
	* @param post $post Post
	*/
	public function login($post)
	{
		unset($_SESSION["auth"]);
		$user = $post["user"];
		$pwd = $post["pwd"];
		$itemuser = $post["itemuser"];
		$itempwd = $post["itempwd"];
		$where = $this->property["where"];
		$this->property["where"] = null;
		$this->dsConnect();
		if (isset($this->property["justthese"]))
		    $r = ldap_search($this->property["conn"], $this->property["base"], 'uid='.$user, $this->property["justthese"]);
		else
		    $r = ldap_search($this->property["conn"], $this->property["base"], 'uid='.$user);
		if ($r)
		{
			$this->property["row"] = ldap_get_entries($this->property["conn"],$r);
			if (isset($this->property["row"][0])) 
			{
				$this->property["row"] = $this->property["row"][0];
				if (ldap_bind($this->property["conn"],$this->property["row"]['dn'], $pwd)) 
				{
				    $_SESSION["auth"]["data"] = @date("d/m/Y H:i:s");
				    $_SESSION["auth"]["user"] = $user;
				    if (isset($this->property["row"]["cn"])) $_SESSION["auth"]["cn"] = $this->property["row"]["cn"];
				    $_SESSION["auth"]["info"] = $this->property["row"];
				    $this->property["row"] = "";
				}
			}
		}
	}

	/**
	* Retrieve information
	*/
	private function getInfoDN($dn)
	{
		$result['dn'] = $dn;
		$explode = explode(",", $result['dn']);
		$result['name'] = $explode[0];
		list($result['type']) = explode("=", $result['name']);
		unset($explode[0]);
		$result['parentdn'] = implode(",", $explode);
		return $result;
	}

	/**
	* Retrieve the list of specified entry
	* @param string $sr result of research
	*/
 	private function getEntry($sr) 
 	{	
		$index = 0;	
		$info = ldap_get_entries($this->property["conn"], $sr) or ClsError::showError("LDAP006");
		for ($i=0; $i<$info["count"]; $i++) 
		{
			$this->property["qrycount"]++; 
 			if ($this->property["qrycount"]<$this->property["start"]+1) continue;
	  		if (($this->property["limit"]>0) && ($this->property["qrycount"]>$this->property["limit"]-1)) break;
			$result = $this->getInfoDN($info[$i]["dn"]);
			$index = count($this->property["result"]); 
			$this->property["result"][$index][$this->property["dskey"]] = $info[$i]["dn"];
			$this->property["result"][$index][$this->property["dsparentkey"]] = $result['parentdn'];
			$this->property["result"][$index][$this->property["dsname"]] = $result['name'];
			$this->property["result"][$index]["type"] = $result['type'];
			$this->sort1[] = $result['parentdn'];
			$this->sort2[] = $result['type'];
			if (isset($result[$this->sortname])) $this->sort3[] = $result[$this->sortname];
			else $this->sort3[] = $result[$this->property["dsname"]];
			foreach ($info[$i] as $key => $value) 
			{
				if (!is_numeric($key)) 
				{
					if ($key=="count") continue;
					if (is_array($value)) 
					{
						unset($value["count"]);
						$this->property["result"][$index][$key] = implode(",", $value);
					}
					else $this->property["result"][$index][$key] = $value;
				}
			}
		}
		return $index;
 	}

	/**
	* Executes a selected query
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
			if (isset($this->justthese)) 
			{
				if ($this->justthese!="") $this->justthese = array_flip(explode(",", $this->justthese));
				else $this->justthese = array();
			}
			switch ($this->scope) 
			{
				case "base":
					if (isset($this->justthese))	
						$sr = ldap_read($this->property["conn"], $sel, $this->filter, $this->justthese) or ClsError::showError("LDAP004");
					else
						$sr = ldap_read($this->property["conn"], $sel, $this->filter) or ClsError::showError("LDAP004");
					$idx = $this->getEntry($sr);
					$this->property["result"][$idx][$this->property["dsparentkey"]] = "";
					$this->property["result"][$idx][$this->property["dsname"]] = $this->alias;
					break;
				case "onelevel":
						if (isset($this->justthese))	
							$sr = ldap_list($this->property["conn"], $sel, $this->filter, $this->justthese) or ClsError::showError("LDAP004");
						else
							$sr = ldap_list($this->property["conn"], $sel, $this->filter) or ClsError::showError("LDAP004");
						$this->getEntry($sr);
					break;
				case "tree":
						if (isset($this->justthese))	
							$sr = ldap_search($this->property["conn"], $sel, $this->filter, $this->justthese) or ClsError::showError("LDAP004");
						else	
							$sr = ldap_search($this->property["conn"], $sel, $this->filter) or ClsError::showError("LDAP004");
						$this->getEntry($sr);
					break;
				default:				
						ClsError::showError("LDAP005");
					break;
			}
 			$this->dsSort();
			if($this->property["debug"]=="true") $system->debug($this->property["id"], "Selected dn: ".$sel);
			$index++;
		}
	}

	/**
	* Recursive function for the recursive deletion of an entry on LDAP
	*/			
	private function ldap_delete_entry($dn)
	{
        $sr = ldap_list($this->property["conn"], $dn, "ObjectClass=*", array(""));
		$info = ldap_get_entries($this->property["conn"], $sr);
        for($i=0;$i<$info['count'];$i++)
		{
            $result = $this->ldap_delete_entry($info[$i]["dn"]);
            if(!$result) return($result);
        }
        return (ldap_delete($this->property["conn"], $dn));
	}

	/**
	* Executes a truncate query
	* @param string $qry Query
	*/
	public function dsQueryDeleteAll($qry = null)
	{
	}
		

	/**
	* Function for recursive deletion of an entry on LDAP
	*/			
	public function dsQueryDelete($select = null)
	{
		global $system;
		if (isset($this->property["select"])) 
		{	
			$this->ldap_delete_entry($this->property["select"]); 
			if($this->property["debug"]=="true") $system->debug($this->property["id"], "Deleted dn: ".$this->property["select"]);
		}
		unset($this->property["select"]);
	}

	/**
	* Function to clone an entry
	*/
	function ldap_clone($ds, $dn, $newdn) 
	{
		$sr = ldap_read($ds, $dn, "(objectClass=*)");
		$entry = ldap_first_entry($ds, $sr);
		$attrs = ldap_get_attributes($ds, $entry);
		$newentry = array();
		for ($i=0; $i<$attrs['count']; $i++) 
		{
			$attribute = $attrs[$i];
			if ($attrs[$attribute]['count'] == 1) $newentry[$attribute] = $attrs[$attribute][0];
			else 
			{
				for ($j=0; $j<$attrs[$attribute]['count']; $j++) 
				{
					$newentry[$attribute][] = $attrs[$attribute][$j];
				}
			}
		}
		return ldap_add($ds, $newdn, $newentry);
	}

	/**
	* 	Function to move an entry on LDAP
	*/			
	private function dsRenameEntry($ds, $dn, $name, $parentdn)
	{
      $sr = ldap_list($ds, $dn, "(objectClass=*)");
		$info = ldap_get_entries($ds, $sr);
		$newdn = $name.",".$parentdn;
		if (!$this->ldap_clone($ds, $dn, $newdn)) return false;
		for($i=0; $i<$info['count']; $i++) 
		{
			list($name) = explode(",", $info[$i]['dn']);
			$this->dsRenameEntry($ds, $info[$i]['dn'], $name, $newdn);
		}
		return true;
	}

	/**
	* Executes a update query
	* @param string $qry Query
	*/
	public function dsQueryUpdate($qry = null)
	{
		global $system;
		$result = $this->getInfoDN($_POST[$_POST['keyname']]);
		$dn 	  = $result["dn"];
		$parentdn = $result["parentdn"];
		$name 	  = $result["name"];
		if (isset($_POST[$this->property["dsname"]])) 
 		{
			$name = $_POST[$this->property["dsname"]];
		}
		if (isset($_POST[$this->property["dsparentkey"]])) 
 		{
			$parentdn = $_POST[$this->property["dsparentkey"]];
		}
 		if ($dn != $parentdn.",".$name) 
		{
 			$this->dsRenameEntry($this->property["conn"], $dn, $name, $parentdn) or ClsError::showError("LDAP008");
	  		$this->ldap_delete_entry($dn);
			if($this->property["debug"]=="true") $system->debug($this->property["id"], "Updated dn: from ".$dn." to ".$name.",".$parentdn);
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
